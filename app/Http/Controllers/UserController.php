<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
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
     * @return \Illuminate\Http\JsonResponse
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
        $authUser = auth()->user();

        $user = User::findOrFail($id);

        if ($authUser->hasRole('admin')) {
            if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $roles = Role::where('name', 'user')->get();
        } elseif ($authUser->hasRole('superadmin')) {
            if ($user->hasRole('superadmin') && $user->id != $authUser->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $roles = Role::whereIn('name', ['user', 'admin'])->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
            'email' => 'required|string|email|unique:users,email,' . $id,
            'role'  => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $users = User::findOrFail($id);

        $users->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $users->password,
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
            'status'=> 'success'
        ]);
    }

    public function index(Request $request) {
        $perPage = $request->input('per_page', 7);
        $authUser = auth()->user();
        $query = User::with('roles')->where('id', '!=', $authUser->id); 

            if ($authUser->hasRole('admin')) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'user');
                });
            } elseif ($authUser->hasRole('superadmin')) {
                $query->whereHas('roles', function($q) {
                    $q->whereIn('name', ['user', 'admin']);
                });
            }
                            
        if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhereHas('roles', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
                });
        }

        $usersPaginator =  $query->paginate($perPage)->withQueryString();

        if ($usersPaginator->currentPage() > $usersPaginator->lastPage() && $usersPaginator->lastPage() > 0) {
                $request->merge(['page' => $usersPaginator->lastPage()]);
                $usersPaginator = $query->paginate($perPage)->withQueryString();
        }

        return response()->json([
            'msg' => 'User Details retrieved successfully',
            'Users' => $usersPaginator->getCollection()->map(function($user) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->roles->pluck('name')
                ];
            }),
            'total'        => $usersPaginator->total(),
            'current_page' => $usersPaginator->currentPage(),
            'last_page'    => $usersPaginator->lastPage(),
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
                'email'=> 'required|email|unique:users,email,' . $user->id,
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
        $totalsocialCount = SocialMedia::count();
        $mypostCount = Post::where('user_id', $user->id)->count();
        $totalRolesCount = Role::count();
        $totalPermissionsCount = Permission::count();
        $latestPosts = [];
        $newUsers = [];

        if($user->hasPermissionTo('view_latest_posts')) {
            $latestPosts = Post::with('user:id,name,email')
                        ->latest()
                        ->take(5)
                        ->get();
        }

        if($user->hasPermissionTo('view_latest_users')) {
            $newUsers = User::select('id','name','email')
                        ->latest()
                        ->take(5)
                        ->get();
        }

        return response()->json([
            'msg' => 'data count fetch successfully',
            'user_count' => $userCount,
            'totalpost_count' => $totalpostCount,
            'mypost_count' => $mypostCount,
            'socialCount' => $totalsocialCount,
            'totalRoles' => $totalRolesCount,
            'totalPermissions' => $totalPermissionsCount,
            'latestPosts' => $latestPosts,
            'newUsers' => $newUsers
        ]);
     }

    /**
    * fetch a user with roles & permissions
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

     public function fetchUser(Request $request) {
        $user = request()->user();

        return response()->json([
            'user' => $user,
            'roles'=> $user->getRoleNames(),
            'permissions'=> $user->getAllPermissions()->pluck('name')
        ]);
     }

    /**
     * Update User Language
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function updateLanguage(Request $request) {
        $request->validate([
            'language' => 'required|in:en,de'
        ]);

        $user = $request->user();
        $user->language = $request->language;
        $user->save();

        return response()->json([
            'status' => 'success',
            'msg' => 'User Language Updated Successfully'
        ]);
     }
}
