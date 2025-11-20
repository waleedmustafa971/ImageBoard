<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\GeoIPService;
use Illuminate\Console\Command;

class TestCountryFlags extends Command
{
    protected $signature = 'test:country-flags {--ip= : Test a specific IP address}';
    protected $description = 'Test the country flag system and optionally update latest post';

    public function handle()
    {
        $geoIPService = new GeoIPService();

        // Test IPs from different countries
        $testIPs = [
            '8.8.8.8' => 'Google DNS (US)',
            '1.1.1.1' => 'Cloudflare (US)',
            '46.51.179.90' => 'UK',
            '213.180.193.3' => 'France',
            '116.58.254.105' => 'Japan',
            '61.14.205.100' => 'China',
            '200.160.2.3' => 'Brazil',
            '41.191.233.100' => 'South Africa',
            '127.0.0.1' => 'Localhost',
        ];

        if ($this->option('ip')) {
            $ip = $this->option('ip');
            $result = $geoIPService->getCountryFromIP($ip);
            $this->info("Testing IP: {$ip}");
            $this->line("Country: {$result['country_name']} ({$result['country_code']})");

            // Ask if user wants to update latest post
            if ($this->confirm('Update the latest post with this country data?', true)) {
                $post = Post::latest()->first();
                if ($post) {
                    $post->update([
                        'country_code' => $result['country_code'],
                        'country_name' => $result['country_name'],
                    ]);
                    $this->info("Updated post #{$post->post_number} with {$result['country_name']} flag");
                } else {
                    $this->error('No posts found in database');
                }
            }
        } else {
            $this->info('Testing GeoIP Service with various IPs:');
            $this->newLine();

            foreach ($testIPs as $ip => $description) {
                $result = $geoIPService->getCountryFromIP($ip);
                $flag = $result['country_code'] !== 'XX'
                    ? "🏴 {$result['country_code']}"
                    : '[Local]';

                $this->line(sprintf(
                    '%-20s %-20s %s - %s',
                    $ip,
                    $description,
                    $flag,
                    $result['country_name']
                ));
            }

            $this->newLine();
            $this->info('Tip: Use --ip=<address> to test a specific IP and update the latest post');
        }

        return 0;
    }
}
