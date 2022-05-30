<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            $query = PermissionGroup::query();

            if ($request->has('q') && strlen($request->input('q')) > 0 ) {
                $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
            }
            $permissionGroups = $query->with('permissions')
                ->get();

            return $this->responseSuccess($permissionGroups);
        } catch (\Exception $e) {
            Log::error('Error get list permission', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
        }
    }
}
