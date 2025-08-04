<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Show all roles
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            $roles = Role::with('permissions')->get();

            return response()->json([
                'status' => 'success',
                'roles'  => $roles,
                'permissions'=> Permission::all()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'error'  => $th->getMessage()
            ]);
        }
    }

    /**
     * Get Role By ID
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        try {
            $role = Role::findOrFail($id);
            $role_permissions = $role->permissions;
            return response()->json([
                'msg' => 'success',
                'roles' => $role,
                'permissions'=> $role_permissions
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg'=> 'error',
                'error'=> $th->getMessage()
            ]);
        }
    }

    /**
     * Get Role by id
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoleBy($id) {
        try {
            $role = Role::findById($id);
            return response()->json([
                'status' => 'success',
                'role' => $role
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Manage Roles
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(RoleRequest $request)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();

            if (isset($validated['role'])) {
                $primary = $validated['role'];

                if (Role::where('name', $primary['name'])->exists()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Role name '{$primary['name']}' is already taken."
                    ], 422);
                }

                $role = Role::create([
                    'name' => $primary['name'],
                    'guard_name' => 'sanctum',
                ]);

                $permissionNames = collect($primary['permissions'] ?? [])
                    ->pluck('name')
                    ->all();

                $role->syncPermissions($permissionNames);

                $message = 'Role created successfully';

            } elseif (isset($validated['roles'])) {
                $roleData = $validated['roles'];

                if (Role::where('name', $roleData['name'])
                        ->where('id', '!=', $roleData['id'])
                        ->exists()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Role name '{$roleData['name']}' is already taken."
                    ], 422);
                }

                $role = Role::findOrFail($roleData['id']);
                $role->update(['name' => $roleData['name']]);

                $permissionIds = collect($roleData['permissions'] ?? [])
                    ->pluck('id')
                    ->map(fn ($val) => (int) $val)
                    ->all();

                $role->syncPermissions($permissionIds);

                $message = 'Role updated successfully';

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No role payload provided.'
                ], 400);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Delete role
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRole($id) {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'msg' => 'Role deleted successfully',
            'status'=> 'success'
        ]);
    }
}
