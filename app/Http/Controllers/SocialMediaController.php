<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;
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

    public function storeWithPosts(Request $request) {
        try {
            $user = request()->user();

            $validated = $request->validate([
                        'platform' => 'required|unique|string',
                        'location' => 'required',
                        'date'     => 'required|date',
                        'forms'        => 'required|array|min:1',
                        'forms.*.name' => 'required|string|max:255',
                        'forms.*.description' => 'required|string|max:255',
                        'forms.*.tags' => 'array|nullable|required',
            ]);

            DB::beginTransaction();

        
            $socialMedia = SocialMedia::create([
                'platform' => $validated['platform'],
                'location' => $validated['location'],
                'date'     => $validated['date']
            ]);

            foreach ($validated['forms'] as $postData) {
                $socialMedia->posts()->create([
                    'name' => $postData['name'],
                    'description' => $postData['description'],
                    'user_id'=> $user->id
                ]);
                if(!empty($postData['tags'])) {
                    $tagIds = collect($postData['tags'])->pluck('id')->toArray();
                    $socialMedia->posts()->tags()->attach($tagIds);
                }
            }
            DB::commit();
            return response()->json([
                'msg' => 'Successfully created Social Media with Posts',
                'data'=> $socialMedia->load('posts')
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
            DB::rollBack();
            return response()->json([
                'msg' => 'Error',
                'Error'=> $th->getMessage()
            ]);
        }
    }
}
