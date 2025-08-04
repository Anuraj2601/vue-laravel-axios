<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * handle register
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request) {
        try {

            $validated = $request->validate([
                'name'  => 'required|string|max:30',
                'email' => 'required|string|email|max:200|unique:users,email',
                'password'  => 'required|string|confirmed|min:8',
                'user_role' => 'required|nullable|string|exists:roles,name',
            ]);

            $user = User::create([
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'password'  => Hash::make($validated['password']),
            ]);

            $roleName = $validated['user_role'] ?? 'user';

            if (Role::where('name', $roleName)->exists()) {
                $user->assignRole($roleName);
            } else {
                $user->assignRole('user');
            }

            $token = $user->createToken('Laravel_Axios')->plainTextToken;

            return response()->json([
                'users' => $user,
                'msg'   => 'User Registered Successfully',
                'status'=> 'success',
                'token' => $token
            ],201);

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
        }catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
        
    }

    /**
     * handle the login
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function login(Request $request) {
        try {
            $validated = $request->validate([
                'email' => 'required|email|string',
                'password'=> 'required|string|min:8'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

                $token = $user->createToken('Laravel_Axios')->plainTextToken;

                return response()->json([
                    'msg'   => 'User Login Successfully',
                    'user' => $user->roles->pluck('name'),
                    'roles'=> $user->getRoleNames(),
                    'permissions'=> $user->getAllPermissions()->pluck('name'),
                    'id'   => $user->id,
                    'name' => $user->name,
                    'token' => $token,
                    'usr'   => $user,
                    'status'    => 'success'
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
                'msg' => $th->getMessage(),
                'line'=> $th->getLine(),
                'file'=> $th->getFile()
            ], 500);
        }
     }
}
