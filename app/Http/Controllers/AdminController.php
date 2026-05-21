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



    public function scrape(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $browser = new HttpBrowser(HttpClient::create());

        try {
            $page = $browser->request('GET', $request->url);

            // Get name
            $name = $page->filter('h1')->count() > 0
                ? trim($page->filter('h1')->text())
                : null;

            // Get price
            $priceText = $page->filter('.product-price')->count() > 0
                ? $page->filter('.product-price')->first()->text()
                : '0';
            $price = (float) preg_replace('/[^0-9.]/', '', $priceText);

            // Get image
            $image = $page->filter('img[alt="' . $name . '"]')->count() > 0
                ? $page->filter('img[alt="' . $name . '"]')->first()->attr('src')
                : null;

            // Get specs from table
            $specs = [];
            $page->filter('table tr')->each(function ($row) use (&$specs) {
                $cells = $row->filter('td');
                if ($cells->count() >= 2) {
                    $key = trim($cells->eq(0)->text());
                    $value = trim($cells->eq(1)->text());
                    $specs[$key] = $value;
                }
            });

            // Extract brand
            $brandMap = [
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
            $brand = 'Unknown';
            foreach ($brandMap as $keyword => $brandName) {
                if (str_contains($name ?? '', $keyword)) {
                    $brand = $brandName;
                    break;
                }
            }

            preg_match('/(\d+GB)\s*RAM/i', $specs['Memory'] ?? '', $ramMatch);
            preg_match('/^(\d+GB)/i', $specs['Memory'] ?? '', $storageMatch);
            preg_match('/(\d+\s*MP)/i', $specs['Rear Camera'] ?? '', $cameraMatch);
            preg_match('/(\d+[\,\d]*\s*mAh)/i', $specs['Battery & Charging'] ?? '', $batteryMatch);
            preg_match('/(\d+\.?\d*")/i', $specs['Display'] ?? '', $screenMatch);
            preg_match('/(iOS|Android)\s*[\d\.]+/i', $specs['Chipset'] ?? '', $osMatch);

            $phone = Phone::updateOrCreate(
                ['store_url' => $request->url],
                [
                    'brand' => $brand,
                    'model' => $name ?? 'Unknown',
                    'price' => $price,
                    'image' => $image ?? 'https://picsum.photos/400/300',
                    'store_url' => $request->url,
                    'ram' => $ramMatch[1] ?? 'N/A',
                    'storage' => $storageMatch[1] ?? 'N/A',
                    'camera' => $cameraMatch[0] ?? 'N/A',
                    'battery' => str_replace(',', '', $batteryMatch[1] ?? 'N/A'),
                    'screen_size' => $screenMatch[1] ?? 'N/A',
                    'os' => $osMatch[0] ?? 'N/A',
                    'release_date' => now()->format('Y-m-d'),
                    'rating' => 0,
                ]
            );

            return redirect()->route('admin.index')->with('success', "✅ Phone '{$phone->model}' scraped successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Failed to scrape: ' . $e->getMessage());
        }
    }


    public function bulkScrape(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->url;
        $siteType = $request->input('site_type', 'auto'); // from frontend

        $client = HttpClient::create([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
        ]);

        $browser = new HttpBrowser($client);

        try {
            // Get site config
            $config = $this->detectSiteConfig($url, $siteType);

            $saved = 0;
            $skipped = 0;
            $allLinks = [];
            $page = 1;
            $maxPages = 15;

            // ===================== PAGINATION =====================
            while (true) {
                $pageUrl = $this->buildPageUrl($url, $page);
                logger()->info("=== Scraping page {$page}: {$pageUrl} ===");

                $crawler = $browser->request('GET', $pageUrl);

                // Debug: See how many links exist on the page
                logger()->info("Total <a> tags on page: " . $crawler->filter('a')->count());
                logger()->info("Total images on page: " . $crawler->filter('img')->count());

                // Try multiple possible selectors for imobi.biz
                $possibleSelectors = [
                    $config['product_link_selector'],
                    'a[href*="/product/"]',
                    'a[href*="/phone"]',
                    '.product-card a',
                    '.item a',
                    'h3 a',
                    'h2 a',
                    '.product-link'
                ];

                $links = [];
                foreach ($possibleSelectors as $selector) {
                    $found = $crawler->filter($selector)->each(function ($node) use ($pageUrl) {
                        $href = $node->attr('href');
                        $text = trim($node->text());
                        if ($href) {
                            $fullUrl = UriResolver::resolve($href, $pageUrl);
                            logger()->info("Found link → " . $fullUrl . " | Text: " . substr($text, 0, 60));
                            return $fullUrl;
                        }
                        return null;
                    });

                    $links = array_merge($links, $found);
                    if (!empty($found)) {
                        logger()->info("Selector '{$selector}' found " . count($found) . " links");
                    }
                }

                $links = array_unique(array_filter($links));

                logger()->info("✅ Final unique product links on page {$page}: " . count($links));

                if (empty($links) || count($links) < 2) {
                    logger()->info("No more products found. Stopping pagination.");
                    break;
                }

                $allLinks = array_merge($allLinks, $links);

                $page++;
                if ($page > $maxPages) {
                    logger()->info("Reached max pages limit ({$maxPages})");
                    break;
                }

                sleep(2); // Longer delay for imobi.biz
            }

            $allLinks = array_unique($allLinks);
            logger()->info("Total unique products found: " . count($allLinks));

            // ===================== SCRAPE EACH PRODUCT =====================
            foreach ($allLinks as $link) {
                try {
                    $this->scrapeSingleProduct($browser, $link, $config, $saved, $skipped);
                    usleep(rand(1500000, 3500000)); // 1.5 to 3.5 seconds delay
                } catch (\Exception $e) {
                    $skipped++;
                    logger()->error("Failed to scrape product: {$link}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return redirect()->route('admin.index')
                ->with('success', "✅ Bulk scrape completed! Saved: {$saved}, Skipped: {$skipped}");
        } catch (\Exception $e) {
            logger()->error('Bulk scrape failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', '❌ Failed: ' . $e->getMessage());
        }
    }

    private function detectSiteConfig(string $url, string $siteType = 'auto'): array
    {
        if ($siteType === 'nika2u' || str_contains($url, 'nika2u.com')) {
            return [
                'name'                  => 'nika2u',
                'product_link_selector' => 'a[href*="/product/"]',
                'name_selector'         => 'h1',
                'price_selectors'       => ['.product-price', '.price', 'h2', 'strong'],
                'image_selector'        => 'img',
                'specs_selector'        => 'table tr',
            ];
        }

        if ($siteType === 'soklyphone' || str_contains($url, 'soklyphone.com')) {
            return [
                'name'                  => 'soklyphone',
                'product_link_selector' => 'a[href*="/product/"], .product-item a, .card a',
                'name_selector'         => 'h1, .product-title, .title',
                'price_selectors'       => ['.price', '.product-price', '.amount'],
                'image_selector'        => 'img',
                'specs_selector'        => 'table tr, .spec-row',
            ];
        }

        if ($siteType === 'imobi' || str_contains($url, 'imobi.biz')) {
            return [
                'name'                  => 'imobi',
                'product_link_selector' => 'a[href*="/product/"], a[href*="/phone"]', // fallback
                'name_selector'         => 'h1, h2, .product-name, .title',
                'price_selectors'       => ['.price', 'h2', 'strong', '[class*="price"]', '.amount'],
                'image_selector'        => 'img',
                'specs_selector'        => 'table tr, .spec, .specification, dl, .specs',
            ];
        }

        throw new \Exception('Unsupported website');
    }

    private function buildPageUrl(string $baseUrl, int $page): string
    {
        return str_contains($baseUrl, '?')
            ? $baseUrl . '&page=' . $page
            : $baseUrl . '?page=' . $page;
    }

    private function scrapeSingleProduct($browser, string $link, array $config, &$saved, &$skipped)
    {
        logger()->info("Processing: " . $link);

        $page = $browser->request('GET', $link);

        // Name
        $name = $page->filter($config['name_selector'])->count() > 0
            ? trim($page->filter($config['name_selector'])->first()->text())
            : null;

        // Price
        $priceText = '0';
        foreach ($config['price_selectors'] as $selector) {
            if ($page->filter($selector)->count() > 0) {
                $priceText = $page->filter($selector)->first()->text();
                break;
            }
        }
        $price = (float) preg_replace('/[^0-9.]/', '', $priceText);

        // Image
        $image = null;
        if ($page->filter($config['image_selector'])->count() > 0) {
            $imgSrc = $page->filter($config['image_selector'])->first()->attr('src');
            if ($imgSrc) {
                $image = UriResolver::resolve($imgSrc, $link);
            }
        }

        // Specs
        $specs = [];
        $page->filter($config['specs_selector'])->each(function ($row) use (&$specs) {
            $cells = $row->filter('td, dt, dd');
            if ($cells->count() >= 2) {
                $key = trim($cells->eq(0)->text());
                $value = trim($cells->eq(1)->text());
                $specs[$key] = $value;
            }
        });

        $brand = $this->detectBrand($name);

        // Extract key specs (you can improve this later)
        preg_match('/(\d+GB)\s*RAM/i', $specs['Memory'] ?? '', $ramMatch);
        preg_match('/^(\d+GB)/i', $specs['Memory'] ?? '', $storageMatch);
        preg_match('/(\d+\s*MP)/i', $specs['Rear Camera'] ?? '', $cameraMatch);
        preg_match('/(\d+[\,\d]*\s*mAh)/i', $specs['Battery'] ?? $specs['Battery & Charging'] ?? '', $batteryMatch);
        preg_match('/(\d+\.?\d*")/i', $specs['Display'] ?? '', $screenMatch);
        preg_match('/(Android|iOS)/i', implode(' ', array_values($specs)), $osMatch);

        if ($name && $price > 0) {
            Phone::updateOrCreate(
                ['store_url' => $link],
                [
                    'brand'        => $brand,
                    'model'        => $name,
                    'price'        => $price,
                    'image'        => $image ?? 'https://picsum.photos/400/300',
                    'store_url'    => $link,
                    'ram'          => $ramMatch[1] ?? 'N/A',
                    'storage'      => $storageMatch[1] ?? 'N/A',
                    'camera'       => $cameraMatch[1] ?? 'N/A',
                    'battery'      => str_replace(',', '', $batteryMatch[1] ?? 'N/A'),
                    'screen_size'  => $screenMatch[1] ?? 'N/A',
                    'os'           => $osMatch[1] ?? 'N/A',
                    'release_date' => now()->format('Y-m-d'),
                    'rating'       => 0,
                ]
            );
            $saved++;
            logger()->info("Saved: {$name}");
        } else {
            $skipped++;
            logger()->warning("Skipped (missing name or price): {$link}");
        }
    }

    private function detectBrand(?string $name): string
    {
        if (!$name) return 'Unknown';

        $brandMap = [
            'iphone'   => 'Apple',
            'samsung'  => 'Samsung',
            'xiaomi'   => 'Xiaomi',
            'redmi'    => 'Xiaomi',
            'oppo'     => 'Oppo',
            'vivo'     => 'Vivo',
            'honor'    => 'Honor',
            'huawei'   => 'Huawei',
            'google'   => 'Google',
            'oneplus'  => 'OnePlus',
            'realme'   => 'Realme',
            'infinix'  => 'Infinix',
            'tecno'    => 'Tecno',
        ];

        $nameLower = strtolower($name);
        foreach ($brandMap as $keyword => $brandName) {
            if (str_contains($nameLower, $keyword)) {
                return $brandName;
            }
        }
        return 'Unknown';
    }
}
