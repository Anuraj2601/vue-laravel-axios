<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocialMediaRequest;
use App\Http\Requests\UpdateSocialMediaRequest;
use App\Http\Requests\UserPostsUpdateRequest;
use App\Models\Post;
use App\Models\SocialMedia;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SocialMediaController extends Controller
{
    /**
     * Fetch Social Media by ID
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id) {
        try {
                $socialMedia = SocialMedia::findOrFail($id);

                if (!$socialMedia) {
                    return response()->json(['error' => 'Social media not found'], 404);
                }

                return response()->json([
                    'msg' => 'Success',
                    'social_media' => $socialMedia
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'Error',
                'error'=> $th->getMessage()
            ]);
        }
    }

    /**
     * Create New Social Media with Posts
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeWithPosts(StoreSocialMediaRequest $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validated();

            DB::beginTransaction();

            $socialMedia = SocialMedia::create([
                'platform' => $validated['platform'],
                'location' => $validated['location'],
                'date'     => $validated['date'],
            ]);

            foreach ($validated['forms'] as $postData) {
                $post = $socialMedia->posts()->create([
                    'name' => $postData['name'],
                    'description' => $postData['description'],
                    'user_id' => $user->id
                ]);

                if (!empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $post->tags()->attach($tagIds);
                }
            }

            DB::commit();

            return response()->json([
                'msg'    => 'Successfully created Social Media with Posts',
                'data'   => $socialMedia->load('posts'),
                'status' => 'success'
            ]);

        } catch (\Exception $th) {
            DB::rollBack();

            Log::error('Error while creating SocialMedia with Posts: ' . $th->getMessage(), [
                'error' => $th,
                'request' => $request->all()
            ]);

            return response()->json([
                'msg'   => 'An error occurred while creating the Social Media entry.',
                'error' => $th->getMessage(),
                'status' => 'error'
            ]);
        }
    }


    /**
     * Manage Social Media & Posts Lists
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateWithPosts(UpdateSocialMediaRequest $request) {
        try {
            $user = $request->user();
            $validated = $request->validated();

            DB::beginTransaction();

            $socialMedia = SocialMedia::find($validated['id']);

            $socialMedia->update([
                'platform' => $validated['platform'],
                'location' => $validated['location'],
                'date'     => $validated['date']
            ]);

            foreach ($validated['forms'] as $postData) {
                $post = $socialMedia->posts()->create([
                    'name' => $postData['name'],
                    'description' => $postData['description'],
                    'user_id'=> $user->id
                ]);
                if(!empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $post->tags()->attach($tagIds);
                }
            }

            foreach ($validated['posts'] as $postData) {
                $post = Post::find($postData['id']);
                $post->name = $postData['name'];
                $post->description = $postData['description'];
                $post->save();

                if(isset($postData['tags']) && !empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $post->tags()->sync($tagIds);
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg'    => 'Social Media Posts Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'msg' => 'error',
                'error'=> $th->getMessage() . ' Line No: ' . $th->getLine()
            ]);
        }
    }

    /**
     * Get Social Media List by User
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function fetchSocialMediaByUser(Request $request) {
        try {
            $user = $request->user();

            $socialMedias = SocialMedia::with(['posts' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('id', 'desc')
            ->paginate(5);

            return response()->json([
                'socialmedia'   => $socialMedias->items(),
                'total'         => $socialMedias->total(),
                'current_page'  => $socialMedias->currentPage(),
                'last_page'     => $socialMedias->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error'=> $th->getMessage() . ' Line at: ' . $th->getLine()
            ]);
        }
    }

    /**
     * Get Social Media posts related particular user with
     * Specific Social Media ID
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function SocPostsByUser($id) {
        try {
            $socialMedia = SocialMedia::find($id);
            $userId        = Auth::id();
            $posts       = $socialMedia->posts()
                                       ->where('user_id',$userId)
                                       ->with('user')
                                       ->get();
            
            $posts->load('tags', 'socialMedia');

            return response()->json([
                'msg' => 'success',
                'posts' => $posts,
                'tags' => Tag::all()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error' => $th->getMessage() . ' Line No: ' . $th->getLine()
            ]);
        }
    }

    /**
     * Update User Posts
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function UpdateUserPosts(UserPostsUpdateRequest $request) {
        try {
            $user = $request->user();
            $validated = $request->validated();

            DB::beginTransaction();

            $socialMedia = SocialMedia::find($validated['id']);

            foreach ($validated['forms'] as $postData) {
                $post = $socialMedia->posts()->create([
                    'name' => $postData['name'],
                    'description' => $postData['description'],
                    'user_id'=> $user->id
                ]);
                if(!empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $post->tags()->attach($tagIds);
                }
            }

            foreach ($validated['posts'] as $postData) {
                $post = Post::find($postData['id']);
                $post->name = $postData['name'];
                $post->description = $postData['description'];
                $post->save();

                if(isset($postData['tags']) && !empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $post->tags()->sync($tagIds);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg'    => 'Social Media Posts Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'msg' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }
}
