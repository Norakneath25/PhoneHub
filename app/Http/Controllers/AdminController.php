<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\UriResolver;
use Symfony\Component\HttpClient\HttpClient;

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
            'brand' => 'required|string',
            'model' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'store_url' => 'required|string',
            'ram' => 'required|string',
            'storage' => 'required|string',
            'camera' => 'required|string',
            'battery' => 'required|string',
            'screen_size' => 'required|string',
            'os' => 'required|string',
            'release_date' => 'required|date',
            'rating' => 'required|numeric',
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
            'url' => 'required|url',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $url = $request->url;
        $limit = $request->input('limit', 10);

        $browser = new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]));

        try {
            $config = $this->getSiteConfig($url);
            $saved = 0;
            $skipped = 0;
            $logs = [];
            $allLinks = $this->discoverProductLinks($browser, $url, $config, $limit, $logs, true);

            foreach ($allLinks as $link) {
                try {
                    $logs[] = $this->scrapeSingleProduct($browser, $link, $config, $saved, $skipped);
                    sleep(1);
                } catch (\Exception $e) {
                    $skipped++;
                    $logs[] = [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                        'url' => $link,
                    ];
                }
            }

            return redirect()->route('admin.index')
                ->with('success', "Scraped {$saved} phones. Skipped: {$skipped}.")
                ->with('scrape_logs', $logs);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed: '.$e->getMessage());
        }
    }

    public function scrapeLinks(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $logs = [];

        try {
            $links = $this->discoverProductLinks(
                $this->makeBrowser(),
                $request->string('url')->toString(),
                $this->getSiteConfig($request->string('url')->toString()),
                (int) $request->input('limit', 10),
                $logs,
                true,
            );

            return response()->json([
                'links' => $links,
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'logs' => $logs,
            ], 422);
        }
    }

    public function scrapeProduct(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $saved = 0;
        $skipped = 0;

        try {
            $log = $this->scrapeSingleProduct(
                $this->makeBrowser(),
                $request->string('url')->toString(),
                $this->getSiteConfig($request->string('url')->toString()),
                $saved,
                $skipped,
            );

            return response()->json([
                'log' => $log,
                'saved' => $saved,
                'skipped' => $skipped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'log' => [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'url' => $request->input('url'),
                ],
                'saved' => 0,
                'skipped' => 1,
            ], 422);
        }
    }

    // =========================================================================
    // Private — Site configuration
    // =========================================================================

    private function getSiteConfig(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        if (str_contains($host, 'imobi.biz')) {
            return [
                'name' => 'imobi',
                'product_link_selector' => '.product-body > a[href*="/en/products/smartphones/"]',
                'product_link_pattern' => '/en/products/smartphones/',
                'category_paths' => [
                    '/en/products/smartphones',
                ],
                'name_selector' => '.right-pro-tit, h1',
                'price_selectors' => [
                    '.right-pro-price',
                    '.pro-des',
                    '.pro-des-res',
                    '[class*="price"]',
                ],
                'image_selector' => 'img[data-u="image"], .w-page-product img',
                'specs_type' => 'gsmarena-table',
            ];
        }

        return [
            'name' => 'nika2u',
            'product_link_selector' => 'a[href*="/product/"]',
            'product_link_pattern' => '/product/',
            'category_paths' => [
                '/products',
            ],
            'name_selector' => 'h1',
            'price_selectors' => [
                '.discounted_unit_price',
                '.unit_price',
                '.product-price',
                '.unit-price',
                '.price',
                '.price-color',
                '[class*="price"]',
            ],
            'image_selector' => 'img',
            'specs_type' => 'gsmarena-table',
        ];
    }

    private function makeBrowser(): HttpBrowser
    {
        return new HttpBrowser(HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ]));
    }

    private function discoverProductLinks(HttpBrowser $browser, string $url, array $config, int $limit, array &$logs, bool $skipExisting = true): array
    {
        $newLinks = [];
        $seenLinks = [];
        $page = 1;
        $maxPages = 10;
        $existingUrls = $skipExisting
            ? Phone::query()->pluck('store_url')->flip()->all()
            : [];

        while (true) {
            $pageUrl = $this->buildPageUrl($url, $page);
            $logs[] = [
                'status' => 'info',
                'message' => "Scanning listing page {$page}",
                'url' => $pageUrl,
            ];

            $crawler = $browser->request('GET', $pageUrl);

            $links = $crawler->filter($config['product_link_selector'])->each(function ($node) use ($pageUrl) {
                $href = $node->attr('href');

                return $href ? UriResolver::resolve($href, $pageUrl) : null;
            });

            $links = array_values(array_unique(array_filter($links)));
            $links = array_values(array_filter($links, fn ($link) => $this->isProductLink($link, $config)));

            if (empty($links)) {
                break;
            }

            $foundUnseenLink = false;

            foreach ($links as $link) {
                if (isset($seenLinks[$link])) {
                    continue;
                }

                $foundUnseenLink = true;
                $seenLinks[$link] = true;

                if ($skipExisting && isset($existingUrls[$link])) {
                    $logs[] = [
                        'status' => 'skipped',
                        'message' => 'Already in catalog, skipping.',
                        'url' => $link,
                    ];

                    continue;
                }

                $newLinks[] = $link;

                if (count($newLinks) >= $limit) {
                    break 2;
                }
            }

            $page++;

            if ($page > $maxPages) {
                break;
            }

            if (! $foundUnseenLink) {
                break;
            }

            sleep(1);
        }

        $logs[] = [
            'status' => 'info',
            'message' => 'Found '.count($newLinks).' new product links.',
        ];

        return $newLinks;
    }

    private function buildPageUrl(string $baseUrl, int $page): string
    {
        if ($page === 1) {
            return $baseUrl;
        }

        return str_contains($baseUrl, '?')
            ? $baseUrl.'&page='.$page
            : $baseUrl.'?page='.$page;
    }

    private function isProductLink(string $url, array $config): bool
    {
        if (! str_contains($url, $config['product_link_pattern'])) {
            return false;
        }

        foreach ($config['category_paths'] as $path) {
            if (rtrim(parse_url($url, PHP_URL_PATH) ?? '', '/') === $path) {
                return false;
            }
        }

        return ! str_contains($url, '/products?');
    }

    // =========================================================================
    // Private — Product scraping
    // =========================================================================

    private function scrapeSingleProduct($browser, string $link, array $config, int &$saved, int &$skipped): array
    {
        $page = $browser->request('GET', $link);

        // Name
        $name = null;
        foreach (explode(',', $config['name_selector']) as $sel) {
            $sel = trim($sel);
            if ($page->filter($sel)->count() > 0) {
                $candidate = trim($page->filter($sel)->first()->text());
                if (strlen($candidate) > 3 && ! in_array(strtolower($candidate), ['home', 'products', 'news', 'support', 'about us'])) {
                    $name = $candidate;
                    break;
                }
            }
        }

        $price = $this->extractPrice($page, $config['price_selectors'], $name);

        // Image & specs
        $image = $this->extractImage($page, $config['image_selector'], $link, $name);
        $specs = $this->extractSpecs($page);

        // Parse spec fields
        $memoryText = $specs['memory'] ?? '';
        $ram = $this->extractRam($memoryText);
        $storage = $this->extractStorage($specs['storage'] ?: $memoryText);

        preg_match('/(\d+\s*MP)/i', $specs['camera'] ?? '', $cameraMatch);
        preg_match('/(\d[\d,]*\s*mAh)/i', $specs['battery'] ?? '', $batteryMatch);
        preg_match('/(\d+\.?\d*"|\d+\.?\d*\s*inches?)/i', $specs['display'] ?? '', $screenMatch);
        preg_match('/(Android|iOS)\s*[\d\.]+/i', $specs['os'] ?? '', $osMatch);

        if ($name && $price > 0) {
            Phone::updateOrCreate(
                ['store_url' => $link],
                [
                    'brand' => $this->detectBrand($name),
                    'model' => $name,
                    'price' => $price,
                    'image' => $image,
                    'store_url' => $link,
                    'ram' => $ram,
                    'storage' => $storage,
                    'camera' => $cameraMatch[0] ?? 'N/A',
                    'battery' => str_replace(',', '', $batteryMatch[1] ?? 'N/A'),
                    'screen_size' => $screenMatch[1] ?? 'N/A',
                    'os' => $osMatch[0] ?? 'N/A',
                    'release_date' => now()->format('Y-m-d'),
                    'rating' => 0,
                ]
            );
            $saved++;

            return [
                'status' => 'saved',
                'message' => "Saved {$name} at $".number_format($price, 2),
                'name' => $name,
                'price' => $price,
                'url' => $link,
            ];
        } else {
            $skipped++;

            return [
                'status' => 'skipped',
                'message' => $name
                    ? "Skipped {$name}: no numeric price found."
                    : 'Skipped product: no valid product name found.',
                'name' => $name,
                'price' => $price,
                'url' => $link,
            ];
        }
    }

    private function extractPrice($page, array $selectors, ?string $name): float
    {
        foreach ($selectors as $selector) {
            if ($page->filter($selector)->count() === 0) {
                continue;
            }

            foreach ($page->filter($selector) as $node) {
                $price = $this->parsePrice($node->textContent ?? '');

                if ($price > 0) {
                    return $price;
                }
            }
        }

        $bodyText = trim($page->filter('body')->text(''));

        if ($name && str_contains($bodyText, $name)) {
            $bodyText = substr($bodyText, strpos($bodyText, $name) + strlen($name));
        }

        return $this->parsePrice($bodyText);
    }

    private function parsePrice(string $text): float
    {
        preg_match_all('/\$\s*((?:[0-9]{1,3}(?:,[0-9]{3})+|[0-9]+)(?:\.[0-9]{2})?)(?![0-9A-Za-z.,])/', $text, $matches);

        foreach ($matches[1] ?? [] as $match) {
            $price = (float) str_replace(',', '', $match);

            if ($price > 0) {
                return $price;
            }
        }

        return 0;
    }

    private function extractRam(string $memoryText): string
    {
        if (preg_match('/(?:^|[^\d])(\d+)\s*G(?:B)?\s*RAM/i', $memoryText, $match)) {
            return $match[1].'GB';
        }

        if (preg_match('/RAM\s*[:\-]?\s*(\d+)\s*G(?:B)?/i', $memoryText, $match)) {
            return $match[1].'GB';
        }

        return 'N/A';
    }

    private function extractStorage(string $memoryText): string
    {
        preg_match_all('/(\d+(?:\.\d+)?)\s*(TB|GB)(?!\s*RAM)/i', $memoryText, $matches, PREG_SET_ORDER);

        $storages = [];

        foreach ($matches as $match) {
            if (strtoupper($match[2]) === 'GB' && (float) $match[1] < 32) {
                continue;
            }

            $value = strtoupper($match[1].$match[2]);

            if (! in_array($value, $storages, true)) {
                $storages[] = $value;
            }
        }

        return $storages ? implode(' / ', $storages) : 'N/A';
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
            '/plugins/jssor/',
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

            if (! $src) {
                return null;
            }
            if (str_starts_with($src, 'data:image')) {
                return null;
            }

            foreach ($skipPatterns as $pattern) {
                if (str_contains($src, $pattern)) {
                    return null;
                }
            }

            $width = (int) $img->attr('width');
            $height = (int) $img->attr('height');
            if (($width > 0 && $width < 50) || ($height > 0 && $height < 50)) {
                return null;
            }

            if (str_contains($src, ',')) {
                $parts = explode(',', $src);
                $src = explode(' ', trim($parts[0]))[0];
            }

            return UriResolver::resolve($src, $url);
        };

        // Priority 1: exact alt-name match
        if ($productName) {
            $altNodes = $page->filter('img[alt="'.addslashes($productName).'"]');
            if ($altNodes->count() > 0) {
                $src = $resolveSrc($altNodes->first());
                if ($src) {
                    return $src;
                }
            }
        }

        // Priority 2: config selector
        $nodes = $page->filter($selector);
        $foundSrc = null;

        if ($nodes->count()) {
            $nodes->each(function ($img) use (&$foundSrc, $resolveSrc) {
                if ($foundSrc) {
                    return;
                }
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
            'memory' => '',
            'storage' => '',
            'camera' => '',
            'battery' => '',
            'display' => '',
            'os' => '',
        ];

        $currentSection = '';

        $page->filter('table tr')->each(function ($row) use (&$specs, &$currentSection) {
            $cells = $row->filter('td');
            if ($cells->count() < 2) {
                return;
            }

            $section = '';
            $label = '';
            $value = '';

            if ($cells->count() >= 3) {
                $section = trim($cells->eq(0)->text());
                $label = trim($cells->eq(1)->text());
                $value = trim($cells->eq(2)->text());

                if ($section !== '' && $section !== "\u{00a0}") {
                    $currentSection = strtolower($section);
                }
            } else {
                $label = trim($cells->eq(0)->text());
                $value = trim($cells->eq(1)->text());
            }

            $key = strtolower(trim($currentSection.' '.$label));

            if (str_contains($key, 'memory') && ($specs['memory'] === '' || str_contains($key, 'internal'))) {
                $specs['memory'] = $value;
            }
            if (str_contains($key, 'internal')) {
                $specs['storage'] = $value;
                $specs['memory'] = $value;
            }
            if (
                str_contains($key, 'camera')
                && ! str_contains($key, 'video')
                && ! str_contains($key, 'features')
                && ($specs['camera'] === '' || str_contains($key, 'main camera'))
            ) {
                $specs['camera'] = $value;
            }
            if (str_contains($key, 'battery') && ($specs['battery'] === '' || str_contains($key, 'type'))) {
                $specs['battery'] = $value;
            }
            if (str_contains($key, 'display') && (str_contains($key, 'size') || $specs['display'] === '')) {
                $specs['display'] = $value;
            }
            if (preg_match('/\bos\b/i', $key) || (str_contains($key, 'chipset') && $specs['os'] === '')) {
                $specs['os'] = $value;
            }
        });

        return $specs;
    }

    // =========================================================================
    // Private — Brand detection
    // =========================================================================

    private function detectBrand(?string $name): string
    {
        $brands = [
            'iPhone' => 'Apple',
            'Samsung' => 'Samsung',
            'Xiaomi' => 'Xiaomi',
            'Oppo' => 'Oppo',
            'Vivo' => 'Vivo',
            'Honor' => 'Honor',
            'Huawei' => 'Huawei',
            'Google' => 'Google',
            'Nothing' => 'Nothing',
            'OnePlus' => 'OnePlus',
            'Infinix' => 'Infinix',
            'Tecno' => 'Tecno',
        ];

        foreach ($brands as $keyword => $brand) {
            if (stripos($name ?? '', $keyword) !== false) {
                return $brand;
            }
        }

        return 'Unknown';
    }
}
