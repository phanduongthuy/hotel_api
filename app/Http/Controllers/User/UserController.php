<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\User\Info\ChangePasswordRequest;
use App\Http\Requests\User\Info\CheckPasswordRequest;
use App\Http\Requests\User\Info\UpdateEmailRequest;
use App\Http\Requests\User\Info\UpdateInfoRequest;
use App\Models\Document;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;
use Exception;

class UserController extends Controller
{
    use ResponseTrait;

    public function getListOrder(Request $request)
    {
        try {
            $userId = '';
            $userCheck = auth()->guard('users')->check();
            if (!$userCheck) {
                if (!$request->has('email')) {
                    return response()->json(['error' => 'Email is require'], 500);
                }
                if (!$request->has('password')) {
                    return response()->json(['error' => 'password is require'], 500);
                }
                if ($this->isExistEmail($request->input('email'))) {
                    $user = User::where('email', $request->input('email'))->first();
                    $userId = $user->_id;
                } else {
                    return response()->json(['error' => 'Email not exist'], 404);
                }

                $credentials = request(['email', 'password']);
                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else {
                    $userCheck = true;
                }
            } else {
                $userId = Auth::id();
            }
            if ($userCheck) {
                $orders = Order::where('user_id', $userId)
                    ->with(['document' => function ($q) {
                        $q->where('type', '=', Document::TYPE['REQUEST']);
                    }, 'languageNative', 'languageTranslate', 'feedback', 'result' => function ($q) {
                        $q->where('type', '=', Document::TYPE['RESULT']);
                    }])
                    ->where('payment_status', '!=', Order::PAYMENT_STATUS['PAID'])
                    ->orderBy('created_at', 'DESC')
                    ->get();

                return $this->responseSuccess($orders);
            }
        } catch (Exception $e) {
            Log::error('Error get list order!', ['method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__]);
            return $this->responseError();
        }
    }

    public function getListOrderForUser(Request $request, $id)
    {
        try {
            $userCheck = auth()->guard('users')->check();
            if ($userCheck) {
                $orders = Order::where('user_id', $id)
                    ->where('payment_status', Order::PAYMENT_STATUS['PAID'])
                    ->with(['document' => function ($q) {
                        $q->where('type', '=', Document::TYPE['REQUEST']);
                    }, 'languageNative', 'languageTranslate', 'feedback', 'result' => function ($q) {
                        $q->where('type', '=', Document::TYPE['RESULT']);
                    }])
                    ->orderBy('created_at', 'DESC')
                    ->get();

                return $this->responseSuccess($orders);
            }
        } catch (Exception $e) {
            Log::error('Error get list order!', ['method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__]);
            return $this->responseError();
        }
    }

    /**
     * @param UpdateInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateInfo(UpdateInfoRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            if ($user) {
                $user->name = $request->input('name');
                $user->gender = $request->input('gender');
                $user->date_of_birth = $request->input('date_of_birth');
                if ($request->hasFile('avatar')) {
                    $path = Storage::disk('public')->putFile('images/avatars', $request->file('avatar'));
                    $user->avatar = $path;
                }

                $user->save();
            } else {
                return $this->responseError('User not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update information!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    /**
     * @param UpdateEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public
    function updateEmail(UpdateEmailRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            if ($user) {
                $credentials = [
                    'user_name' => $user->user_name,
                    'password' => $request->input('current_password'),
                ];
                if (!auth()->attempt($credentials)) {
                    $error = ['current_password' => 'Mật khẩu hiện tại không chính xác'];
                    return $this->responseError('error', $error, 400);
                }
                if ($this->isExistEmail($request->input('email'), auth()->id())) {
                    $error = ['email' => ['Email đã tồn tại.']];
                    return $this->responseError('error', $error, 400);
                }
                $user->email = $request->input('email');

                $user->save();
            } else {
                return $this->responseError('User not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update email!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function updatePhoneNumber(Request $request)
    {
        try {
            $user = User::find(auth()->id());

            if ($user) {
                if ($request->has('phone')) {
                    if ($this->isExistPhone($request->input('phone'), auth()->id())) {
                        $error = ['phone' => ['Số điện thoại đã tồn tại.']];
                        return $this->responseError('error', $error, 400);
                    }
                    $user->phone = $request->input('phone');
                }
                $user->save();
            } else {
                return $this->responseError('User not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update user phone number', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function updateAuthPassword(ChangePasswordRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            if ($user) {
                $credentials = [
                    'user_name' => $user->user_name,
                    'password' => $request->input('current_password'),
                ];

                if (!auth()->attempt($credentials)) {
                    $error = ['current_password' => ['Mật khẩu hiện tại không chính xác']];
                    return $this->responseError('error', $error, 400);
                }

                if ($request->input('password') === $request->input('current_password')) {
                    $error = ['password' => ['Mật khẩu mới phải khác mật khẩu hiện tại']];
                    return $this->responseError('error', $error, 400);
                }

                $user->password = Hash::make($request->input('password'));
                $user->save();
            } else {
                return $this->responseError('User not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error change user password', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function feedback(Request $request)
    {
        try {
            $feedback = Feedback::where('user_id', Auth::user()->_id)->with('document', 'order')->get();

            return $this->responseSuccess($feedback);
        } catch (Exception $e) {
            Log::error('Error get feedback', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            if ($this->isExistEmail($request->input('email'))) {
                $user = User::where('user_name', $request->input('email'))->first();
                $user->password = Hash::make($request->input('password'));
                $user->save();
            } else {
                return $this->responseError('User not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error change password', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    /**
     * @param $email
     * @return bool
     */
    private
    function isExistEmail($email)
    {
        $count = User::where('email', $email)->count();
        return $count > 0;
    }

    /**
     * @param $phone
     * @return bool
     */
    private
    function isExistPhone($phone)
    {
        $count = User::where('phone', $phone)
            ->where('_id', '<>', Auth::id())
            ->count();
        return $count > 0;
    }

    public function getListOrderPayment(Request $request) {
        try {
            $userId = '';
            $userCheck = auth()->guard('users')->check();
            if (!$userCheck) {
                if (!$request->has('email')) {
                    return response()->json(['error' => 'Email is require'], 500);
                }
                if (!$request->has('password')) {
                    return response()->json(['error' => 'password is require'], 500);
                }
                if ($this->isExistEmail($request->input('email'))) {
                    $user = User::where('email', $request->input('email'))->first();
                    $userId = $user->_id;
                } else {
                    return response()->json(['error' => 'Email not exist'], 404);
                }

                $credentials = request(['email', 'password']);
                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else {
                    $userCheck = true;
                }
            } else {
                $userId = Auth::id();
            }
            if ($userCheck) {
                $orders = Order::where('user_id', $userId)
                    ->where('payment_status', '!=', Order::PAYMENT_STATUS['PAID'])
                    ->where('status','>', Order::STATUS['NO_PRICE'])
                    ->whereIn('_id', $request->orderIds)
                    ->get();

                return $this->responseSuccess($orders);
            }
        } catch (Exception $e) {
            Log::error('Error get list order payment', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function checkPassword(CheckPasswordRequest $request)
    {
        $credentials = request(['user_name', 'password']);

        if (!auth()->attempt($credentials)) {
            return $this->responseError('Mật khẩu không chính xác!', [], 400);
        }else {
            return $this->responseSuccess();
        }

    }
}
