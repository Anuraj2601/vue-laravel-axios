<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* $socialMediaPlatforms = [
            ['platform' => 'Facebook' , 'url' => 'https://facebook.com'],
            ['platform' => 'Twitter', 'url' => 'https://twitter.com'],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com'],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com'],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com']
        ];

        foreach ($socialMediaPlatforms as $platform) {
            SocialMedia::create([
                'platform'  => $platform['platform'],
                'url'   => $platform['url']
            ]);
        } */
    }
}
