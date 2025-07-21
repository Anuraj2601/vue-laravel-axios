<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Create new user
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        return response()->json([
            'redirect' => route('user.create'),
        ]);
    }

    /**
     * Store new user
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'email' => 'required|string|email',
        ]);

        $user = User::create([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'password'=> Hash::make('password')
        ]);

        $token = $user->createToken('Laravel_Axios')->plainTextToken;

        return response()->json([
            'msg'   => 'User Created Successfully',
            'users' => $user,
            'token' => $token
        ],201);
    }

    /**
     * Edit the user
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function Edit($id) {
        $user = User::findOrFail($id);

        $roles = Role::all();

        return response()->json([
            'user' => $user,
            'roles'=> $roles,
            'user_role' => $user->roles()->pluck('name')
        ]);
    }

    /**
     * Update the user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,$id) {
       try {
         $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'email' => 'required|string|email',
            'role'  => 'required|exists:roles,name'
        ]);

        $users = User::findOrFail($id);

        $users->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        $users->syncRoles($validated['role']);

        return response()->json([
            'msg' => 'User Updated Successfully',
            'status'=> 'success',
            'user' => $users
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
        }  catch (\Throwable $th) {
             return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'User updated error'
            ]);
       }
    }

    public function destory($id) {
        $users = User::findOrFail($id);

        $users->delete();

        return response()->json([
            'msg' => 'User Deleted Successfully',
            'status'=> ' success'
        ]);
    }

    public function index() {
        $users = User::with('roles')->get();
        return response()->json([
            'msg' => 'User Details retrieved successfully',
            'Users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->pluck('name')
                ];
            })
        ]);
    }

    /**
     * User Profile Update
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function profileUpdate(Request $request) {
        try {
            $user = auth()->user();

            $request->validate([
                'name' => 'required|string',
                'email'=> 'required|email',
                'image'=> 'required|max:2048'
            ]);


            if($request->hasFile('image')) {
                if($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }

                $path = $request->file('image')->store('profile_images', 'public');
                $user->image = $path;

            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'status' => 'success',
                'user'   => $user
            ]);
        }catch (ValidationException $e) {
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
                'status'    => 'error',
                'msg' => $th->getMessage()
            ]);
        }
     }

    /**
     * Retrieved User Profile
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */

     public function show($id) {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'status'=> 'error',
                'msg' => 'User not found'
            ],404);
        }

        return response()->json([
            'status' => 'success',
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->roles()->pluck('name'),
                'image'     => $user->image ? asset('storage/' . $user->image) : null
            ]
        ]);
     }

    
    /**
     * fetch data count
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse 
     */

     public function getCount() {
        $user = auth()->user();

        $userCount = User::count();
        $totalpostCount = Post::count();
        $mypostCount = Post::where('user_id', $user->id)->count();

        return response()->json([
            'msg' => 'data count fetch successfully',
            'user_count' => $userCount,
            'totalpost_count' => $totalpostCount,
            'mypost_count' => $mypostCount
        ]);
     }
}
