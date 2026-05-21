<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\UriResolver;

class AdminController extends Controller
{
    // =========================================================================
    // CRUD — Phones
    // =========================================================================

    public function index()
    {
        $phones = Phone::all();

        return Inertia::render('Admin/Index', ['phones' => $phones]);
    }

    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand'        => 'required|string',
            'model'        => 'required|string',
            'price'        => 'required|numeric',
            'image'        => 'required|string',
            'store_url'    => 'required|string',
            'ram'          => 'required|string',
            'storage'      => 'required|string',
            'camera'       => 'required|string',
            'battery'      => 'required|string',
            'screen_size'  => 'required|string',
            'os'           => 'required|string',
            'release_date' => 'required|date',
            'rating'       => 'required|numeric',
        ]);

        Phone::create($request->all());

        return redirect()->route('admin.index');
    }

    public function edit(string $id)
    {
        $phone = Phone::findOrFail($id);

        return Inertia::render('Admin/Edit', ['phone' => $phone]);
    }

    public function update(Request $request, string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->update($request->all());

        return redirect()->route('admin.index');
    }

    public function destroy(string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->delete();

        return redirect()->route('admin.index');
    }

    // =========================================================================
    // CRUD — Reviews
    // =========================================================================

    public function reviews()
    {
        $reviews = Review::with(['phone', 'user'])->get();

        return Inertia::render('Admin/Reviews', ['reviews' => $reviews]);
    }

    public function destroyReview(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews');
    }

    // =========================================================================
    // Scraping — Bulk (listing page)
    // =========================================================================

    public function bulkScrape(Request $request)
    {
        $request->validate([
            'url'   => 'required|url',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $url   = $request->url;
        $limit = $request->input('limit', 10);

        $browser = new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]));

        try {
            $config   = $this->getSiteConfig();
            $saved    = 0;
            $skipped  = 0;
            $allLinks = [];
            $page     = 1;
            $maxPages = 10;

            while (true) {
                $pageUrl = $this->buildPageUrl($url, $page);
                $crawler = $browser->request('GET', $pageUrl);

                $links = $crawler->filter($config['product_link_selector'])->each(function ($node) use ($pageUrl) {
                    $href = $node->attr('href');
                    return $href ? UriResolver::resolve($href, $pageUrl) : null;
                });

                $links = array_unique(array_filter($links));
                $links = array_filter($links, fn($l) => $this->isProductLink($l));

                if (empty($links)) break;

                $allLinks = array_merge($allLinks, $links);
                $page++;

                if ($page > $maxPages || count($allLinks) >= $limit) break;

                sleep(1);
            }

            $allLinks = array_slice(array_unique($allLinks), 0, $limit);

            foreach ($allLinks as $link) {
                try {
                    $this->scrapeSingleProduct($browser, $link, $config, $saved, $skipped);
                    sleep(1);
                } catch (\Exception $e) {
                    $skipped++;
                }
            }

            return redirect()->route('admin.index')
                ->with('success', "✅ Scraped {$saved} phones! (Skipped: {$skipped})");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Failed: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Private — Site configuration
    // =========================================================================

    private function getSiteConfig(): array
    {
        return [
            'name'                  => 'nika2u',
            'product_link_selector' => 'a[href*="/product/"]',
            'name_selector'         => 'h1',
            'price_selectors'       => ['.product-price'],
            'image_selector'        => 'img',
            'specs_type'            => 'nika2u',
        ];
    }

    private function buildPageUrl(string $baseUrl, int $page): string
    {
        if ($page === 1) return $baseUrl;

        return str_contains($baseUrl, '?')
            ? $baseUrl . '&page=' . $page
            : $baseUrl . '?page=' . $page;
    }

    private function isProductLink(string $url): bool
    {
        return str_contains($url, '/product/') && !str_contains($url, '/products');
    }

    // =========================================================================
    // Private — Product scraping
    // =========================================================================

    private function scrapeSingleProduct($browser, string $link, array $config, int &$saved, int &$skipped): void
    {
        $page = $browser->request('GET', $link);

        // Name
        $name = null;
        foreach (explode(',', $config['name_selector']) as $sel) {
            $sel = trim($sel);
            if ($page->filter($sel)->count() > 0) {
                $candidate = trim($page->filter($sel)->first()->text());
                if (strlen($candidate) > 3 && !in_array(strtolower($candidate), ['home', 'products', 'news', 'support', 'about us'])) {
                    $name = $candidate;
                    break;
                }
            }
        }

        // Price
        $priceText = '0';
        foreach ($config['price_selectors'] as $selector) {
            if ($page->filter($selector)->count() > 0) {
                $text = trim($page->filter($selector)->first()->text());
                if (preg_match('/\$\s*[\d,]+/', $text)) {
                    $priceText = $text;
                    break;
                }
            }
        }
        preg_match('/\$\s*([\d,]+(?:\.\d+)?)/', $priceText, $priceMatch);
        $price = isset($priceMatch[1]) ? (float) str_replace(',', '', $priceMatch[1]) : 0;

        // Image & specs
        $image = $this->extractImage($page, $config['image_selector'], $link, $name);
        $specs = $this->extractSpecs($page);

        // Parse spec fields
        preg_match('/(\d+\s*G(?:B)?)\s*RAM/i',              $specs['memory']  ?? '', $ramMatch);
        preg_match('/(\d+(?:GB|TB))/i',                      $specs['storage'] ?? $specs['memory'] ?? '', $storageMatch);
        preg_match('/(\d+\s*MP)/i',                          $specs['camera']  ?? '', $cameraMatch);
        preg_match('/(\d[\d,]*\s*mAh)/i',                    $specs['battery'] ?? '', $batteryMatch);
        preg_match('/(\d+\.?\d*"|\d+\.?\d*\s*inches?)/i',   $specs['display'] ?? '', $screenMatch);
        preg_match('/(Android|iOS)\s*[\d\.]+/i',             $specs['os']      ?? '', $osMatch);

        // Normalise RAM: "12G" → "12GB"
        $ram = isset($ramMatch[1]) ? rtrim($ramMatch[1]) : 'N/A';
        if (preg_match('/^\d+G$/', $ram)) $ram .= 'B';

        if ($name && $price > 0) {
            Phone::updateOrCreate(
                ['store_url' => $link],
                [
                    'brand'        => $this->detectBrand($name),
                    'model'        => $name,
                    'price'        => $price,
                    'image'        => $image,
                    'store_url'    => $link,
                    'ram'          => $ram,
                    'storage'      => $storageMatch[1]                      ?? 'N/A',
                    'camera'       => $cameraMatch[0]                       ?? 'N/A',
                    'battery'      => str_replace(',', '', $batteryMatch[1] ?? 'N/A'),
                    'screen_size'  => $screenMatch[1]                       ?? 'N/A',
                    'os'           => $osMatch[0]                           ?? 'N/A',
                    'release_date' => now()->format('Y-m-d'),
                    'rating'       => 0,
                ]
            );
            $saved++;
        } else {
            $skipped++;
        }
    }

    // =========================================================================
    // Private — Image extraction
    // =========================================================================

    private function extractImage($page, string $selector, string $url, ?string $productName = null): string
    {
        $fallback = 'https://picsum.photos/400/300';

        $skipPatterns = [
            'facebook.com/tr',
            'google-analytics.com',
            'googletagmanager.com',
            'doubleclick.net',
            'analytics',
            'beacon',
            'pixel.',
            '/tr?',
        ];

        $resolveSrc = function ($img) use ($url, $skipPatterns): ?string {
            $src = $img->attr('src')
                ?? $img->attr('data-src')
                ?? $img->attr('data-lazy-src')
                ?? $img->attr('data-original')
                ?? null;

            if (!$src) return null;
            if (str_starts_with($src, 'data:image')) return null;

            foreach ($skipPatterns as $pattern) {
                if (str_contains($src, $pattern)) return null;
            }

            $width  = (int) $img->attr('width');
            $height = (int) $img->attr('height');
            if (($width > 0 && $width < 50) || ($height > 0 && $height < 50)) return null;

            if (str_contains($src, ',')) {
                $parts = explode(',', $src);
                $src   = explode(' ', trim($parts[0]))[0];
            }

            return UriResolver::resolve($src, $url);
        };

        // Priority 1: exact alt-name match
        if ($productName) {
            $altNodes = $page->filter('img[alt="' . addslashes($productName) . '"]');
            if ($altNodes->count() > 0) {
                $src = $resolveSrc($altNodes->first());
                if ($src) return $src;
            }
        }

        // Priority 2: config selector
        $nodes    = $page->filter($selector);
        $foundSrc = null;

        if ($nodes->count()) {
            $nodes->each(function ($img) use (&$foundSrc, $resolveSrc) {
                if ($foundSrc) return;
                $foundSrc = $resolveSrc($img);
            });
        }

        return $foundSrc ?? $fallback;
    }

    // =========================================================================
    // Private — Spec extraction (Nika2u)
    // =========================================================================

    private function extractSpecs($page): array
    {
        $specs = [
            'memory'  => '',
            'storage' => '',
            'camera'  => '',
            'battery' => '',
            'display' => '',
            'os'      => '',
        ];

        $page->filter('table tr')->each(function ($row) use (&$specs) {
            $cells = $row->filter('td');
            if ($cells->count() < 2) return;

            $key   = strtolower(trim($cells->eq(0)->text()));
            $value = trim($cells->eq(1)->text());

            if (str_contains($key, 'memory'))                               $specs['memory']  = $value;
            if (str_contains($key, 'camera'))                               $specs['camera']  = $value;
            if (str_contains($key, 'battery'))                              $specs['battery'] = $value;
            if (str_contains($key, 'display'))                              $specs['display'] = $value;
            if (str_contains($key, 'chipset') || str_contains($key, 'os')) $specs['os']      = $value;
        });

        return $specs;
    }

    // =========================================================================
    // Private — Brand detection
    // =========================================================================

    private function detectBrand(?string $name): string
    {
        $brands = [
            'iPhone'  => 'Apple',
            'Samsung' => 'Samsung',
            'Xiaomi'  => 'Xiaomi',
            'Oppo'    => 'Oppo',
            'Vivo'    => 'Vivo',
            'Honor'   => 'Honor',
            'Huawei'  => 'Huawei',
            'Google'  => 'Google',
            'Nothing' => 'Nothing',
            'OnePlus' => 'OnePlus',
            'Infinix' => 'Infinix',
            'Tecno'   => 'Tecno',
        ];

        foreach ($brands as $keyword => $brand) {
            if (stripos($name ?? '', $keyword) !== false) {
                return $brand;
            }
        }

        return 'Unknown';
    }
}