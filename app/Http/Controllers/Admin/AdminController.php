<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\User\ChangeActiveStatusRequest;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%')
                ->orWhere('email', 'like', '%' . $request->input('q') . '%')
                ->orWhere('phone', 'like', '%' . $request->input('q') . '%');
        }

        $users = $query->with('wallet')->paginate(config('constants.per_page'));
        return $this->responseSuccess($users);
    }


    /**
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {


            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store user', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    /**
     * @param UpdateAuthInfoRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAuthInfoRequest $request, $id)
    {
        try {
            $auth = User::findOrFail($id);

            $auth->name = $request->input('name');
            $auth->email = $request->input('email');
            $auth->is_active = (boolean)$request->input('is_active');

            if ($request->has('phone')) {
                if ($this->isExistPhone($request->input('phone'), $id)) {
                    $error = ['phone' => ['Số điện thoại đã tồn tại.']];
                    return $this->responseError('error', $error, 400);
                }

                if (!$this->isPhoneNumber($request->input('phone'))) {
                    $error = ['phone' => ['Số điện thoại sai định dạng.']];
                    return $this->responseError('error', $error, 500);
                }

                $auth->phone = $request->input('phone');
            }

            if ($request->hasFile('avatar')) {
                $path = Storage::disk('public')->putFile('images/users', $request->file('avatar'));
                $auth->avatar = $path;
            }

            $auth->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update auth user info', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    /**
     * @param $id
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($id, ResetPasswordRequest $request)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return $this->responseSuccess();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $products = UserProduct::query()->where('user_id', $id)->get();
        if (!empty($products)) {
            $ids = $products->pluck('_id')->toArray();
            UserProductKeyword::whereIn('user_product_id', $ids)->delete();
            UserProduct::where('user_id', $id)->delete();
        }

        ShopUser::where('user_id', $id)->delete();
        ShopUserFollow::where('user_id', $id)->delete();
        Task::where('user_id', $user->_id)->delete();
        Wallet::where('user_id', $id)->delete();

        $user->delete();
        return $this->responseSuccess();
    }

    /**
     * @param $presenterCode
     * @return bool
     */
    public function isExistPresenterCode($presenterCode)
    {
        $count = User::where('presenter_code', $presenterCode)
            ->whereNull('deleted_at')
            ->count();

        return $count > 0;
    }

    /**
     * @param $presentCode
     * @return boolean
     */
    public function isExistPresentCode($presentCode)
    {
        $count = User::where('present_code', $presentCode)
            ->whereNull('deleted_at')
            ->count();

        return $count > 0;
    }

    /**
     * @param $phone
     * @return bool|int
     */
    private function isPhoneNumber($phone)
    {
        if ($phone == '') {
            return true;
        }
        return preg_match(config('constants.is_phone_number'), $phone);
    }

    /**
     * @param $phone
     * @param $userId
     * @return bool
     */
    private function isExistPhone($phone, $userId)
    {
        $count = User::where('phone', $phone)->where('_id', '<>', $userId)->count();
        return $count > 0;
    }

    /**
     * @param ChangeActiveStatusRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeActiveStatus(ChangeActiveStatusRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = (boolean)$request->input('is_active');
            $user->save();

            if (!$user->is_active) {
                UserProduct::where('user_id', $id)->update(['is_active' => false]);
                ShopUser::where('user_id', $id)->update(['is_active' => false]);
                ShopUserFollow::where('user_id', $id)->update(['is_active' => false]);
                Task::where('user_id', $user->_id)->delete();
                $userProductIds = UserProduct::where('user_id', $id)->pluck('_id');
                UserProductKeyword::whereIn("user_product_id", $userProductIds)->update(['can_use' => false]);
            }

            if ($user->is_active) {
                UserProduct::where('user_id', $id)->update(['is_active' => true]);
                ShopUser::where('user_id', $id)->update(['is_active' => true]);
                ShopUserFollow::where('user_id', $id)->update(['is_active' => true]);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error("Error change active status user", [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }
}
