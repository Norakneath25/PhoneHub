<?php

namespace App\Console\Commands;

use App\Models\Phone;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ScrapePhones extends Command
{
    protected $signature = 'phones:scrape';
    protected $description = 'Scrape phones from nika2u.com';

    public function handle()
    {
        $browser = new HttpBrowser(HttpClient::create());

        $this->info('Fetching phone list...');

        $crawler = $browser->request('GET', 'https://web.nika2u.com/products?category_id=1&data_from=category');

        $links = $crawler->filter('a')->each(function ($node) {
            $href = $node->attr('href');
            if ($href && str_contains($href, '/product/')) {
                return $href;
            }
            return null;
        });

        $links = array_unique(array_filter($links));

        $this->info('Found ' . count($links) . ' phones. Scraping...');

        foreach ($links as $link) {
            try {
                $page = $browser->request('GET', $link);

                // Get name
                $name = $page->filter('h1')->count() > 0
                    ? trim($page->filter('h1')->text())
                    : null;

                // Get price using correct selector
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

                // Extract brand from name
                $brandMap = ['iPhone' => 'Apple', 'Samsung' => 'Samsung', 'Xiaomi' => 'Xiaomi', 'Oppo' => 'Oppo', 'Vivo' => 'Vivo', 'Honor' => 'Honor', 'Huawei' => 'Huawei', 'Google' => 'Google', 'Nothing' => 'Nothing', 'OnePlus' => 'OnePlus', 'Infinix' => 'Infinix', 'Tecno' => 'Tecno', 'Realme' => 'Realme'];
                $brand = 'Unknown';
                foreach ($brandMap as $keyword => $brandName) {
                    if (str_contains($name, $keyword)) {
                        $brand = $brandName;
                        break;
                    }
                }

                $model = $name;

                // Extract RAM
                preg_match('/(\d+GB)\s*RAM/i', $specs['Memory'] ?? '', $ramMatch);
                $ram = $ramMatch[1] ?? 'N/A';

                // Extract storage
                preg_match('/^(\d+GB)/i', $specs['Memory'] ?? '', $storageMatch);
                $storage = $storageMatch[1] ?? 'N/A';

                // Extract camera
                preg_match('/(\d+\s*MP)/i', $specs['Rear Camera'] ?? '', $cameraMatch);
                $camera = $cameraMatch[0] ?? 'N/A';

                // Extract battery
                preg_match('/(\d+[\,\d]*\s*mAh)/i', $specs['Battery & Charging'] ?? '', $batteryMatch);
                $battery = str_replace(',', '', $batteryMatch[1] ?? 'N/A');

                // Extract screen size
                preg_match('/(\d+\.?\d*")/i', $specs['Display'] ?? '', $screenMatch);
                $screen_size = $screenMatch[1] ?? 'N/A';

                // Extract OS
                preg_match('/(iOS|Android)\s*[\d\.]+/i', $specs['Chipset'] ?? '', $osMatch);
                $os = $osMatch[0] ?? 'N/A';

                if ($name && $price > 0) {
                    Phone::updateOrCreate(
                        ['store_url' => $link],
                        [
                            'brand' => $brand,
                            'model' => $model,
                            'price' => $price,
                            'image' => $image ?? 'https://picsum.photos/400/300',
                            'store_url' => $link,
                            'ram' => $ram,
                            'storage' => $storage,
                            'camera' => $camera,
                            'battery' => $battery,
                            'screen_size' => $screen_size,
                            'os' => $os,
                            'release_date' => now()->format('Y-m-d'),
                            'rating' => 0,
                        ]
                    );

                    $this->info("✅ Saved: $name - $$price");
                } else {
                    $this->warn("⚠️ Skipped: $link (name: $name, price: $price)");
                }

                sleep(1);
            } catch (\Exception $e) {
                $this->error("❌ Error on $link: " . $e->getMessage());
            }
        }

        $this->info('Done!');
    }
}
