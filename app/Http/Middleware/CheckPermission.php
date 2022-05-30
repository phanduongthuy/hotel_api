<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissionName)
    {
        try {
            $employee = auth()->user();
            $role = Role::find($employee->role_id);
            if ($role) {
                $isSuperAdmin = $this->hasPermission($role, 'super-admin');
                if ($isSuperAdmin) {
                    return $next($request);
                }

                $isPermission = $this->hasPermission($role, $permissionName);
                if ($isPermission) {
                    return $next($request);
                }
            }
            return $this->responseError(
                'error',
                ['error_permission' => ['Bạn không có quyền truy cập tính năng này']],
                Response::HTTP_FORBIDDEN,
                403
            );
        } catch (\Exception $e) {
            Log::error('Error middleware permission for employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            $this->responseError();
        }

    }

    /**
     * @param $role
     * @param $permissionName
     * @return bool
     */
    private function hasPermission($role, $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        $permissionId = in_array($permission->_id, $role['permission_ids']);
        return $permissionId;
    }
}
