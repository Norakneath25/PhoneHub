<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public static function posts(): array
    {
        return [
            [
                'slug' => 'samsung-galaxy-s25-ultra-review',
                'category' => 'Reviews',
                'title' => 'Samsung Galaxy S25 Ultra: The Pinnacle of Android in 2025',
                'excerpt' => 'After two weeks of daily driving the S25 Ultra, we break down whether the titanium build, 200MP camera, and Snapdragon 8 Elite justify the flagship price tag.',
                'content' => <<<'HTML'
<p>The Samsung Galaxy S25 Ultra arrives as the undisputed king of Android flagships. With a redesigned titanium frame, an embedded S Pen, and a 200MP main camera, it sets a new bar for what a smartphone can do.</p>
<h2>Design & Build</h2>
<p>The titanium chassis feels premium in the hand — lighter than you'd expect given the 6.9" display. Samsung has smoothed the corners compared to the S24 Ultra, making it noticeably more comfortable for one-handed use.</p>
<h2>Camera System</h2>
<p>The 200MP main sensor captures extraordinary detail in daylight. Low-light performance has improved significantly thanks to a wider aperture and improved AI processing. The 5x periscope telephoto remains one of the best zoom cameras on any phone.</p>
<h2>Performance</h2>
<p>Snapdragon 8 Elite handles everything thrown at it without breaking a sweat. Gaming, 8K video recording, and heavy multitasking all feel effortless. Battery life comfortably lasts a full day with heavy use.</p>
<h2>Verdict</h2>
<p>If you want the absolute best Android experience money can buy, the Galaxy S25 Ultra delivers. The S Pen integration, pro-grade cameras, and class-leading performance make it easy to recommend — if the price doesn't scare you off.</p>
HTML,
                'author' => 'Dara Sok',
                'read_time' => '8 min read',
                'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&q=80',
                'featured' => true,
                'tags' => json_encode(['Samsung', 'Android', 'Flagship']),
            ],
            [
                'slug' => 'iphone-17-air-everything-we-know',
                'category' => 'News',
                'title' => 'iPhone 17 Air: Apple Goes Ultra-Thin with a Surprise',
                'excerpt' => "Leaked schematics confirm Apple's thinnest iPhone ever at just 5.5mm. Here's everything we know about the rumored specs, release date, and pricing.",
                'content' => <<<'HTML'
<p>Apple is reportedly preparing its most dramatic iPhone redesign in years. The iPhone 17 Air is rumored to measure just 5.5mm thick, making it slimmer than an iPod nano — and packed with surprising specs for its form factor.</p>
<h2>What We Know So Far</h2>
<p>Multiple supply chain sources point to a 6.6-inch OLED display with ProMotion 120Hz refresh. Despite the slim profile, Apple is expected to include a single 48MP rear camera and an upgraded 24MP front camera.</p>
<h2>Chassis & Materials</h2>
<p>The Air is said to feature an aluminum frame — a deliberate choice to hit the slim target — paired with a new composite back material that Apple claims is more durable than glass.</p>
<h2>Release & Price</h2>
<p>Analysts expect a September 2025 announcement alongside the standard iPhone 17 lineup. Pricing is rumored to start around $899, positioning it between the standard and Pro models.</p>
HTML,
                'author' => 'Mina Chan',
                'read_time' => '5 min read',
                'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&q=80',
                'featured' => false,
                'tags' => json_encode(['Apple', 'iPhone', 'Leaks']),
            ],
            [
                'slug' => 'pixel-9-pro-vs-iphone-16-pro-camera',
                'category' => 'Comparisons',
                'title' => 'Pixel 9 Pro vs iPhone 16 Pro: Which Camera Wins?',
                'excerpt' => 'We shot 500+ photos across landscapes, portraits, and low-light scenes. The results might surprise even the most loyal fans on both sides.',
                'content' => <<<'HTML'
<p>Google and Apple make the two best smartphone cameras on the market. But which one wins in a real-world head-to-head? We spent three weeks shooting with both to find out.</p>
<h2>Daylight Photography</h2>
<p>Both phones produce stunning daylight shots. The Pixel 9 Pro leans toward natural color science, while the iPhone 16 Pro tends to be slightly punchier and contrastier. Neither is wrong — it comes down to personal preference.</p>
<h2>Low-Light Performance</h2>
<p>This is where Google's computational photography shines. Night Sight on the Pixel 9 Pro consistently pulls more detail from dark scenes. The iPhone 16 Pro is excellent but slightly noisier in very low light.</p>
<h2>Portrait Mode</h2>
<p>Apple's Portrait mode produces more consistent edge detection. Google's version is impressive but occasionally struggles with complex backgrounds like tree branches.</p>
<h2>Video</h2>
<p>iPhone 16 Pro wins video — no contest. Log video, Action mode, and the overall color science make it the go-to for videographers.</p>
<h2>Verdict</h2>
<p>For photography: Pixel 9 Pro. For video: iPhone 16 Pro. Both are extraordinary — your ecosystem preference should be the deciding factor.</p>
HTML,
                'author' => 'Ratha Lim',
                'read_time' => '12 min read',
                'image' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&q=80',
                'featured' => false,
                'tags' => json_encode(['Google', 'Apple', 'Camera']),
            ],
            [
                'slug' => 'hidden-android-15-features',
                'category' => 'Tips & Tricks',
                'title' => "10 Hidden Android 15 Features You're Probably Not Using",
                'excerpt' => 'From adaptive refresh shortcuts to the redesigned notification shade, Android 15 packed in dozens of quality-of-life improvements that flew under the radar.',
                'content' => <<<'HTML'
<p>Android 15 launched with a lot of fanfare around its headline features, but the best additions are buried in settings menus most people never open. Here are 10 worth knowing about.</p>
<h2>1. Adaptive Refresh Rate Shortcut</h2>
<p>You can now toggle between 60Hz and high refresh rates directly from Quick Settings without diving into Display settings.</p>
<h2>2. Per-App Language Settings</h2>
<p>Set individual apps to run in different languages independently of your system language — great for language learners.</p>
<h2>3. Improved Predictive Back Gesture</h2>
<p>The back gesture now shows a live preview of where you'll land before you complete the swipe.</p>
<h2>4. Satellite Messaging Support</h2>
<p>Android 15 adds native API support for satellite messaging on supported hardware.</p>
<h2>5. Health Connect Overhaul</h2>
<p>The Health Connect dashboard now aggregates data more intelligently across fitness and health apps.</p>
<h2>6. Theft Detection Lock</h2>
<p>Using on-device AI, your phone can detect if it's been snatched and automatically lock the screen.</p>
<h2>7. Private Space</h2>
<p>Create a hidden, locked section of your phone with separate apps, notifications, and a different PIN.</p>
<h2>8. PDF Viewer Annotations</h2>
<p>The built-in PDF viewer now supports highlighting, drawing, and text annotations without a third-party app.</p>
<h2>9. Louder Speakers Mode</h2>
<p>A new audio setting allows the speakers to temporarily exceed normal limits for media playback.</p>
<h2>10. Lock Screen Shortcuts</h2>
<p>Fully customizable lock screen shortcuts — replace the default flashlight and camera with any app or shortcut you choose.</p>
HTML,
                'author' => 'Dara Sok',
                'read_time' => '6 min read',
                'image' => 'https://images.unsplash.com/photo-1607252650355-f7fd0460ccdb?w=800&q=80',
                'featured' => false,
                'tags' => json_encode(['Android', 'Tips']),
            ],
            [
                'slug' => 'xiaomi-15-pro-review',
                'category' => 'Reviews',
                'title' => 'Xiaomi 15 Pro Review: Leica Camera, Killer Price',
                'excerpt' => "Xiaomi's partnership with Leica continues to deliver stunning results. We test the 15 Pro's 1-inch sensor against the best in the business.",
                'content' => <<<'HTML'
<p>Xiaomi's partnership with Leica has produced some of the most interesting camera systems in the Android world. The Xiaomi 15 Pro takes this collaboration to new heights with a 1-inch Sony LYT-900 sensor.</p>
<h2>Camera</h2>
<p>The 1-inch sensor captures significantly more light than competitors, resulting in exceptional dynamic range and low-light shots. Leica's color profiles — Leica Authentic and Leica Vibrant — give photographers real creative control over the final image look.</p>
<h2>Display</h2>
<p>The 6.73" LTPO AMOLED display with 144Hz adaptive refresh rate is among the best available. Peak brightness hits 3200 nits, making it perfectly readable even in direct sunlight.</p>
<h2>Performance & Battery</h2>
<p>Snapdragon 8 Elite paired with 12GB RAM keeps things snappy. The 5200mAh battery with 90W wired and 50W wireless charging means you're never waiting long to top up.</p>
<h2>Verdict</h2>
<p>At its price point, the Xiaomi 15 Pro offers a camera system that genuinely challenges phones costing hundreds more. If you can live without the Google ecosystem, this is an incredible value flagship.</p>
HTML,
                'author' => 'Mina Chan',
                'read_time' => '9 min read',
                'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&q=80',
                'featured' => false,
                'tags' => json_encode(['Xiaomi', 'Leica', 'Camera']),
            ],
            [
                'slug' => 'oneplus-13t-announced',
                'category' => 'News',
                'title' => 'OnePlus 13T Announced: 6000mAh Battery, 100W Charging',
                'excerpt' => 'OnePlus breaks the endurance barrier with a new T-series flagship that promises two full days of battery life and a 100W wired charging solution.',
                'content' => <<<'HTML'
<p>OnePlus has officially unveiled the 13T, a battery-focused flagship that aims to solve the one problem most power users still complain about: running out of charge before the day ends.</p>
<h2>Battery & Charging</h2>
<p>The headline spec is a massive 6000mAh silicon-carbon battery paired with 100W SUPERVOOC wired charging. OnePlus claims a full charge in under 30 minutes, and two days of battery life under normal use.</p>
<h2>Display</h2>
<p>A 6.82" 1.5K LTPO AMOLED display with 120Hz adaptive refresh rate handles the visuals. It's bright, colorful, and smooth — everything you'd expect from a 2025 flagship.</p>
<h2>Cameras</h2>
<p>A 50MP main sensor, 50MP ultrawide, and 32MP telephoto round out the triple-camera system. OnePlus has worked with Hasselblad on the color tuning again.</p>
<h2>Availability</h2>
<p>The OnePlus 13T will be available globally from June 2025, starting at $699 for the base 12GB/256GB model.</p>
HTML,
                'author' => 'Ratha Lim',
                'read_time' => '4 min read',
                'image' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&q=80',
                'featured' => false,
                'tags' => json_encode(['OnePlus', 'Battery', 'Flagship']),
            ],
        ];
    }

    public function run(): void
    {
        foreach (self::posts() as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
