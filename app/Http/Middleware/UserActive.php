<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActive
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->is_active) {
                return $this->responseError(
                    'error',
                    [
                        'error_active' => ['Tài khoản của bạn đã bị khóa']
                    ],
                    Response::HTTP_UNAUTHORIZED,
                    401
                );
            }
            return $next($request);
        }
    }
}
