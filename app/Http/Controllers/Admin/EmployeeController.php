<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Employee\StoreEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Exception;

class EmployeeController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Admin::query()->with('role');
            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where('name', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('email', 'LIKE', '%' . $request->input('q') . '%');
            }
            $employees = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

            return $this->responseSuccess($employees);
        } catch (Exception $e) {
            Log::error('Error get list employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            if ($this->isExistEmails($request->input('email'))) {
                $error = ['email' => ['Email đã tồn tại.']];
                return $this->responseError('error', $error, 400);
            }

            $employee = new Admin();
            $employee->name = $request->input('name');
            $employee->email = $request->input('email');
            $employee->phone = $request->input('phone');
            $employee->gender = (boolean)$request->input('gender');
            $employee->password = Hash::make($request->input('password'));
            $employee->address = $request->input('address');
            $employee->role_id = $request->input('role_id');
            $employee->date_of_birth = $request->input('date_of_birth');
            if ($request->hasFile('avatar')) {
                $path = Storage::disk('public')->putFile('images/admins', $request->file('avatar'));
                $employee->avatar = $path;
            }

            $employee->save();
            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error store employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Admin::find($id);
            if ($employee) {
                if ($request->has('email')) {
                    if ($this->isExistEmail($request->input('email'), $id)) {
                        $error = ['email' => ['Email đã tồn tại.']];
                        return $this->responseError('error', $error, 400);
                    }
                    $employee->email = $request->input('email');
                }
                if ($request->has('name')) {
                    $employee->name = $request->input('name');
                }
                if ($request->has('phone')) {
                    $employee->phone = $request->input('phone');
                }
                if ($request->input('address') == "null") {
                    $employee->address = null;
                } else {
                    $employee->address = $request->input('address');
                }
                if ($request->hasFile('avatar')) {
                    $path = Storage::disk('public')->putFile('images/admins', $request->file('avatar'));
                    $employee->avatar = $path;
                }
                if ($request->has('gender')) {
                    $employee->gender = (boolean)$request->input('gender');
                }
                if ($request->has('date_of_birth')) {
                    $employee->date_of_birth = $request->input('date_of_birth');
                }
                if ($request->has('role_id')) {
                    $employee->role_id = $request->input('role_id');
                }

                $employee->save();
            } else {
                return $this->responseError('Không có admin này trong hệ thống!', [], 404);
            }
            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }


    public function changePassword(Request $request, $id)
    {
        try {
            $admin = Admin::findOrFail($id);
            $admin->password = Hash::make($request->input('password'));
            $admin->save();
            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error change password shop', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        try {
            $admin = Admin::find($id);
            if ($admin) {
                $admin->delete();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Employee not found', [], 404);
            }
        } catch (Exception $e) {
            Log::error('Error delete employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    private function isExistEmails($email)
    {
        $count = Admin::where('email', $email)->count();
        return $count > 0;
    }

    private function isExistEmail($email, $adminId)
    {
        $count = Admin::where('email', $email)->where('_id', '<>', $adminId)->count();
        return $count > 0;
    }
}
