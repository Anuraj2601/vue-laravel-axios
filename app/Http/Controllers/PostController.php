<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Create a post
     * @return \Illuminate\Http\JsonResponse
     */

     public function create() {
        $tag = Tag::all();
        return response()->json([
            'tags'     => $tag
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
                    'forms.*.tags' => 'array|nullable|required'
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
        $post->load('tags');
        
        return response()->json([
            'post' => $post,
            'tags' => Tag::all()
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
            ]);

            $post = Post::findOrFail($id);

            $post->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            $post->tags()->sync($validated['tags']);

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
        $query = $posts = Post::with(['user:id,name', 'tags:name'])->orderBy('id', 'desc');

        if($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
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
     }

    /**
     * Show only user's posts
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function show(Request $request) {
        try {
                $query = Post::with(['tags:name'])
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'desc');

            if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
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

}
