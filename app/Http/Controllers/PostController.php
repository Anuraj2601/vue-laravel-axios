<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SocialMedia;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Create a post
     * @return \Illuminate\Http\JsonResponse
     */

     public function create() {
        $tag = Tag::all();
        $socialMedia = SocialMedia::all();
        return response()->json([
            'tags'     => $tag,
            'socialMedia'=> $socialMedia
        ]);
     }

    /**
     * Store the post
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function store(Request $request) {
        try {
                $data = $request->validate([
                    'forms'        => 'required|array',
                    'forms.*.name' => 'required|string|max:255',
                    'forms.*.description' => 'required|string|max:255',
                    'forms.*.tags' => 'array|nullable|required',
                    'forms.*.socialMedia'=> 'array|nullable|required'
                ]);

                foreach($data['forms'] as $form) {
                    $post = Post::create([
                        'name'  => $form['name'],
                        'description'=> $form['description'],
                        'user_id'   => Auth::id()
                ]);

                    if(!empty($form['tags'])) {
                        $tagIds = collect($form['tags'])->pluck('id')->toArray();
                        $post->tags()->attach($tagIds);
                    }

                    if(!empty($form['socialMedia'])) {
                        $socialMediaIds = collect($form['socialMedia'])->pluck('id')->toArray();
                        $post->socialMedia()->attach($socialMediaIds);
                    }
                }

                return response()->json([
                    'msg' => 'Post created succcessfully',
                    'status'=> 'success',
                    'posts' => $data
                ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            
            $formattedErrors = [];
                foreach ($errors as $field => $messages) {
                    $formattedErrors[$field] = $messages[0];  
            }

            return response()->json([
                'msg' => 'Validation Errors',
                'error' => $formattedErrors  
            ], 422);   
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'Post added error'
            ]);
        }
     }

    
    /**
     * Edit the post
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

     public function edit($id) {
        $post = Post::findOrFail($id);
        $post->load('tags', 'socialMedia');
        
        return response()->json([
            'post' => $post,
            'tags' => Tag::all(),
            'socialMedia'=> SocialMedia::all()
        ]);
     }

    /**
     * Update the post
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function update(Request $request, $id) {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'tags' => 'array|nullable',
                'tags.*' => 'exists:tags,id',
                'socialMedia' => 'array|nullable',
                'socialMedia.*'=> 'exists:socialMedia,id'
            ]);

            $post = Post::findOrFail($id);

            $post->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            $post->tags()->sync($validated['tags']);
            $post->socialMedia()->sync($validated['socialMedia']);

            return response()->json([
                'msg' => 'Post updated successfully',
                'status'=> 'success',
                'posts' => $post
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            
            $formattedErrors = [];
                foreach ($errors as $field => $messages) {
                    $formattedErrors[$field] = $messages[0];  
            }

            return response()->json([
                'msg' => 'Validation Errors',
                'error' => $formattedErrors  
            ], 422);   
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'user updated error'
            ]);
        }
     }

    /**
     * delete the post
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

     public function destory($id) {
        $post = Post::findOrFail($id);

        $post->delete();
        return response()->json([
            'msg' => 'Post deleted successfully',
            'status'=> 'success'
        ]);
     }

    /**
     * Show all the posts
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function index(Request $request) {
        try {
            $query = $posts = Post::with(['user:id,name', 'tags:name', 'socialMedia:platform,id'])->orderBy('id', 'desc');

            if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
                });
            }

            if($request->has('socialMediaType') && $request->socialMediaType != '') {
                    $socialMediaType = $request->socialMediaType;
                    $query->whereHas('socialMedia', function($q) use ($socialMediaType) {
                        $q->where('social_media_id', $socialMediaType);
                    });
            }

            $posts = $query->paginate(5);
            return response()->json([
                'msg' => 'Retrieved all posts successfully',
                'posts' => $posts->items(),
                'total' => $posts->total(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'status'=> 'success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error'=>$th->getMessage()
            ]);
        }
     }

    /**
     * Show only user's posts
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function show(Request $request) {
        try {
                $query = Post::with(['tags:name', 'socialMedia:platform,id'])
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'desc');

            if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
                });
            }

            if ($request->has('socialMediaType') && $request->socialMediaType != '') {
                $socialMediaType = $request->socialMediaType;
                $query->whereHas('socialMedia', function($q) use ($socialMediaType) {
                    $q->where('social_media_id', $socialMediaType);
                });
            }

            $posts = $query->paginate(5);
            return response()->json([
                'msg' => 'Retrieved user posts successfully',
                'posts' => $posts->items(),
                'total' => $posts->total(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'status'=> 'success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'posts retrieved error'
            ]);
        }
     }

    /**
     * Posts by Social Media Id
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

     public function postbySoc($id) {
        try {
            $posts = SocialMedia::find($id)->posts;
            if($posts->isEmpty()) {
                return response()->json([
                    'msg' => 'No posts found for this platform'
                ],404);
            }
            $posts->load('tags', 'socialMedia');

            return response()->json([
                'msg' => 'success',
                'posts'=> $posts,
                'tags' => Tag::all(),
                'socialMedia'=> SocialMedia::all()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error' => $th->getMessage()
            ]);
        }
     }

    /**
     * Update Multiple Posts
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function updateAll(Request $request) {
        try {
            $validated = $request->validate([
                'posts'        => 'required|array',
                'posts.*.id'   => 'required|exists:posts,id',
                'posts.*.name' => 'required|string|max:255',
                'posts.*.description' => 'required|string|max:255',
                'posts.*.tags' => 'array|nullable|required',
                /* 'posts.*.social_media'=> 'array|nullable|required' */
            ]);

            Log::info('Update Post Details.. ', $validated);
            foreach ($validated as $postData) {
                 if (!isset($postData['id'])) {
                    Log::warning('Missing ID in postData!', ['postData' => $postData]);
                    continue;
                }
                $id = $postData['id'];
                $post = Post::find($id);
                $post->name = $postData['name'];
                $post->description = $postData['description'];
                $post->save();

                if(isset($postData['tags']) && !empty($postData['tags'])) {
                    $post->tags()->sync($postData['tags']);
                }

                /* if(isset($postData['social_media']) && !empty($postData['social_media'])) {
                    $post->socialMedia()->sync($postData['social_media']);
                } */
            }

            return response()->json([
                'status' => 'success',
                'msg' => 'Post Updated Successfully'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            
            $formattedErrors = [];
                foreach ($errors as $field => $messages) {
                    $formattedErrors[$field] = $messages[0];  
            }

            return response()->json([
                'msg' => 'Validation Errors',
                'error' => $formattedErrors  
            ], 422);   
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Post Updated error ' . $th->getMessage() . ' ' . $th->getLine()
            ]);
        }
     }

}
