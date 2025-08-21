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
    public function index(Request $request) {
        try {
            $perPage = $request->input('per_page', 7);
            $query = $rolesPaginator = Role::with('permissions')
                        ->orderBy('id','desc');

            if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhereHas('permissions', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%$search%");
                        });
                });
            }
                        
            $rolesPaginator = $query->paginate($perPage)->withQueryString();

            if ($rolesPaginator->currentPage() > $rolesPaginator->lastPage() && $rolesPaginator->lastPage() > 0) {
                $request->merge(['page' => $rolesPaginator->lastPage()]);
                $rolesPaginator = $query->paginate($perPage)->withQueryString();
            }

            return response()->json([
                'status' => 'success',
                'roles'  => $rolesPaginator->items(),
                'total'        => $rolesPaginator->total(),
                'current_page' => $rolesPaginator->currentPage(),
                'last_page'    => $rolesPaginator->lastPage(),
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
     * 
     * @return void
     */
    public function getUserRolesforCreate(Request $request) {
        try {
            $authUser = auth()->user();

            $query = Role::with('permissions')->orderBy('id','desc');

            // Filter roles based on logged-in user's role
            if ($authUser->hasRole('admin')) {
                // Admin can only see 'user' role
                $query->where('name', 'user');
            } elseif ($authUser->hasRole('superadmin')) {
                // Superadmin can see 'user' and 'admin' roles
                $query->whereIn('name', ['user', 'admin']);
            } else {
                // Normal users cannot see any roles
                $query->whereRaw('0 = 1'); // empty result
            }

            $roles = $query->get();

            return response()->json($roles);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
                'permission'=> $role_permissions,
                'permissions' => Permission::all()

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
