<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\User\Auth\UserRegisterRequest;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Laravel\Socialite\Facades\Socialite;
use function PHPUnit\Framework\returnArgument;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function userLogin()
    {
        $credentials = request(['user_name', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function userRegister(UserRegisterRequest $request)
    {
        try {
            $user = new User();
            $user->user_name = $request->input('user_name');
            $user->email = $request->input('user_name');
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error user register', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
            return $this->responseError();
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function me()
    {
        $user = auth()->user();
        if (empty($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        };
        if (!empty($user->role_id)) {
            $role = Role::find($user->role_id);
            if ($role->permission_ids) {
                $permissionNames = Permission::whereIn('_id', $role->permission_ids)->get(['name']);
                $user['permissions'] = $permissionNames;
            } else {
                $user['permissions'] = [];
            }
        } else {
            $user['permissions'] = [];
        }
        return response()->json($user);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function updateAuthPassword(ChangePasswordRequest $request)
    {
        try {
            $user = Admin::find(auth()->id());
            if ($user) {
                $credentials = [
                    'email' => $user->email,
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

    public function redirectToGoogle(Request $request)
    {
        return Response::json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function redirectToFacebook(Request $request)
    {
        return Response::json([
            'url' => Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function loginGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();
            if ($user) {
                $user->password = Hash::make('Transflash@2022');
                $user->save();
                $credentials = [
                    'email'=>$googleUser->email,
                    'password'=>'Transflash@2022'
                ];
                $token = auth()->attempt($credentials);
                return $this->respondWithToken($token);
            } else {
                $user = User::create(
                    [
                        'email' => $googleUser->email,
                        'user_name' => $googleUser->email,
                        'name' => $googleUser->name,
                        'password' => Hash::make('Transflash@2022')
                    ]
                );
                $credentials = [
                    'user_name'=>$googleUser->email,
                    'password'=>'Transflash@2022'
                ];

                $token = auth()->attempt($credentials);
                return $this->respondWithToken($token);
            }
        } catch (Exception $e) {
            Log::error('Error login with google', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    public function loginFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();
            if ($fbUser->email) {
                $user = User::where('email', $fbUser->email)->first();
                if ($user) {
                    $user->password = Hash::make('Transflash@2022');
                    $user->facebook_id = $fbUser->id;
                    $user->save();
                    $credentials = [
                        'email'     =>  $user->email,
                        'password'  =>  'Transflash@2022'
                    ];
                    $token = auth()->attempt($credentials);
                    return $this->respondWithToken($token);
                } else {
                    $newUser = new User();
                    $newUser->email = $fbUser->email;
                    $newUser->user_name = $fbUser->email;
                    $newUser->name = $fbUser->name;
                    $newUser->password = Hash::make('Transflash@2022');
                    $newUser->facebook_id = $fbUser->id;
                    $newUser->save();
                    $credentials = [
                        'user_name'=>$fbUser->email,
                        'password'=>'Transflash@2022'
                    ];

                    $token = auth()->attempt($credentials);
                    return $this->respondWithToken($token);
                }
            } else {
                $newUser = new User();
                $newUser->email = $fbUser->id . '@gmail.com';
                $newUser->user_name = $fbUser->id . '@gmail.com';
                $newUser->name = $fbUser->name;
                $newUser->password = Hash::make('123456');
                $newUser->facebook_id = $fbUser->id;
                $newUser->save();
                $credentials = [
                    'user_name'=>$newUser->user_name,
                    'password'=>'123456'
                ];

                $token = auth()->attempt($credentials);
                return $this->respondWithToken($token);
            }

        } catch (Exception $e) {
            Log::error('Error login with facebook', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    public function handleLoginWithSosialSDK(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->password     = Hash::make('Transflash@2022');
                $user->google_id    = $request->has('googleId')? $request->googleId : '';
                $user->facebook_id  = $request->has('facebookId')? $request->facebookId : '';

                $user->save();
                $credentials = [
                    'email'     =>  $request->email,
                    'password'  =>  'Transflash@2022'
                ];
                $token = auth()->attempt($credentials);
                return $this->respondWithToken($token);
            } else {
                if(!$request->has('email') && $request->has('facebookId')) {
                    $newUser = User::create(
                        [
                            'email'         => $request->facebookId . '@gmail.com',
                            'user_name'     => $request->facebookId . '@gmail.com',
                            'name'          => $request->name,
                            'google_id'     => $request->has('googleId')? $request->googleId : '' ,
                            'facebook_id'   => $request->has('facebookId')? $request->facebookId : '',
                            'password'      => Hash::make('Transflash@2022')
                        ]
                    );
                } else {
                    $newUser = User::create(
                        [
                            'email'         => $request->email,
                            'user_name'     => $request->email,
                            'name'          => $request->name,
                            'google_id'     => $request->has('googleId')? $request->googleId : '' ,
                            'facebook_id'   => $request->has('facebookId')? $request->facebookId : '',
                            'password'      => Hash::make('Transflash@2022')
                        ]
                    );
                }

                $credentials = [
                    'user_name' =>  $newUser->email,
                    'password'  =>  'Transflash@2022'
                ];

                $token = auth()->attempt($credentials);
                return $this->respondWithToken($token);
            }
        } catch (Exception $e) {
            Log::error('Error login with Social network', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

}
