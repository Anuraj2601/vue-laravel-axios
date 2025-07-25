<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $socialMediaData = [
            ['platform' => 'Facebook', 'url' => 'https://facebook.com', 'location' => 'USA', 'date' => Carbon::now()->toDateString()],
            ['platform' => 'Twitter', 'url' => 'https://twitter.com', 'location' => 'USA', 'date' => Carbon::now()->toDateString()],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com', 'location' => 'USA', 'date' => Carbon::now()->toDateString()],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com', 'location' => 'USA', 'date' => Carbon::now()->toDateString()],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com', 'location' => 'USA', 'date' => Carbon::now()->toDateString()],
        ];

        foreach ($socialMediaData as $platform) {
            DB::table('social_media')->updateOrInsert(
                ['platform' => $platform['platform']],
                $platform,
            );
        }
    }
}
