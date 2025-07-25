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
        /* $postIds = [43,44,45,46,49,51,52,54,55,56];

        foreach ($postIds as $postId) {
            $socialMedia = SocialMedia::inRandomOrder()->first();
            $post = Post::find($postId);
            
            if($post && $socialMedia) {
                $post->social_media_id = $socialMedia->id;
                $post->save();
            }
        } */
    }
}
