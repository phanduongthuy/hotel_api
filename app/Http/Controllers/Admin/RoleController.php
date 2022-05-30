<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index (Request $request)
    {
        $query = Role::query();
        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }
        $roles = $query->orderBy('created_at','DESC')->paginate(config('constants.per_page'));

        return $this->responseSuccess($roles);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $role = Role::find($id);
        return $this->responseSuccess($role);
    }

    /**
     * @param StoreRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = new Role();
            $role->name = $request->input('name');
            $role->is_protected = false;
            $role->description = $request->input('description');
            $role->save();

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error store role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            return $this->responseError();
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                if ($this->isExistRole($request->input('name'), $id)) {
                    $error = ['error_name' => ['Vai trò đã tồn tại.']];
                    return $this->responseError('error', $error, Response::HTTP_BAD_REQUEST, 400);
                }
                $role->name = $request->input('name');
                $role->description = $request->input('description');

                $role->save();
            } else {
                $error = ['error_role' => ['Vai trò không tồn tại.']];
                return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        Role::destroy($id);
        return $this->responseSuccess();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermissionForRole(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                if ($request->has('permission_id')) {
                    $permissionIds = $this->getPermission($request->input('permission_id'));
                    $role->permissions()->attach($permissionIds);
                    $role->save();
                } else {
                    $error = ['error_permission' => ['Quyền không tồn tại']];
                    return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
                }
            } else {
                $error = ['error_role' => ['Vai trò không tồn tại.']];
                return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error add permission for role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            return $this->responseError();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePermissionForRole(Request $request ,$id)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                if ($request->has('permission_id')) {
                    $permissionIds = $this->getPermission($request->input('permission_id'));
                    $role->permissions()->detach($permissionIds);
                    $role->save();
                } else {
                    $error = ['error_permission' => ['Quyền không tồn tại']];
                    return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
                }
            } else {
                $error = ['error_role' => ['Vai trò không tồn tại.']];
                return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error remove permission for role', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            return $this->responseError();
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRoles()
    {
        $roles = Role::orderBy('created_at','DESC')->latest()->get();
        return $this->responseSuccess($roles);
    }

    /**
     * @param $permissionId
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getPermission($permissionId)
    {
        $permissionIds = [];
        $permission = Permission::find($permissionId);
        $permissionGroup = PermissionGroup::find($permissionId);
        if (!$permission && !$permissionGroup) {
            $error = ['error_permission' => ['Quyền này không tồn tại']];
            return $this->responseError('error', $error, Response::HTTP_NOT_FOUND, 404);
        }
        if ($permissionGroup) {
            $permissionIds = Permission::where('permission_group_code', $permissionGroup->code)->pluck('_id')->toArray();
        }
        if ($permission) {
            array_push($permissionIds, $permission->_id);
        }
        return $permissionIds;
    }

    /**
     * @param $name
     * @param $id
     * @return bool
     */
    public function isExistRole($name, $id)
    {
        $count = Role::where('name', $name)
            ->where('_id', '<>', $id)
            ->count();
        return $count > 0;
    }
}
