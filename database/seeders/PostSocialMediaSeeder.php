<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\SocialMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* $postIds = [10,13,14,17,34,35,36,38,39,40,42];

        foreach ($postIds as $postId) {
            $socialMedia = SocialMedia::inRandomOrder()->limit(3)->get();

            foreach ($socialMedia as $platform) {
                $post = Post::find($postId);
                $post->socialMedia()->attach($platform->id);
            }
        } */
    }
}
