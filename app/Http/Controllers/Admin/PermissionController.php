<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Get All permissions
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            $perPage = $request->input('per_page', 7);
            $query = $permissionsPaginator = Permission::orderBy('id','desc');

            if($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            }
            
            $permissionsPaginator = $query->paginate($perPage)->withQueryString();

            if ($permissionsPaginator->currentPage() > $permissionsPaginator->lastPage() && $permissionsPaginator->lastPage() > 0) {
                $request->merge(['page' => $permissionsPaginator->lastPage()]);
                $permissionsPaginator = $query->paginate($perPage)->withQueryString();
            }

            return response()->json([
                'msg'           => 'success',
                'permissions'   => $permissionsPaginator->items(),
                'total'         => $permissionsPaginator->total(),
                'current_page'  => $permissionsPaginator->currentPage(),
                'last_page'     => $permissionsPaginator->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error'=> $th->getMessage()
            ]);
        }
    }


    /**
     * Get Permission by id
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissionById($id) {
        try {
            $permission = Permission::findById($id);
            return response()->json([
                'status' => 'success',
                'permission' => $permission
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Manage Permissions
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeWithUpdate(PermissionRequest $request)
    {
        try {
            $validated = $request->validated();

            

            /* if (!empty($validated['permissions'])) {
                    $validator = Validator::make($validated['permissions'], [
                        'name' => [
                            'required',
                            'string',
                            Rule::unique('permissions', 'name')->ignore($validated['permissions']['id']),
                        ],
                    ]);

                    if ($validator->fails()) {
                        $errors['permissions.name'] = $validator->errors()->first('name');
                    }
            } */

            /* if (!empty($errors)) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $errors
                ], 422);
            } */

            DB::beginTransaction();

            if (!empty($validated['forms'])) {
                    Permission::create([
                        'name' => $validated['forms']['name']
                    ]);
            }

            if (!empty($validated['permissions'])) {
                    $permission = Permission::find($validated['permissions']['id']);
                    $permission->update(['name' => $validated['permissions']['name']]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg'    => 'Permissions created/updated successfully'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Server error occurred',
                'error'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete permissions
     * @param \Illuminate\Http\Request $id
     * @return \Illuminate\http\JsonResponse
     */
    public function deletePermission($id) {
        try {
            if (!$id || !is_numeric($id)) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Invalid permission ID'
                ], 400);
            }

            $permission = Permission::findOrFail($id);

            $permission->delete();
            return response()->json([
                'msg' => 'Permission deleted successfully',
                'status'=> 'success'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Permission not found'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'msg'=> 'error',
                'error'=> $th->getMessage() . ' Line No: ' . $th->getLine()
            ]);
        }
    }
}
