<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('q') && strlen($request->input('q')) > 0) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('user_name', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('phone', 'LIKE', "%" . $request->input('q') . "%");
            });
        }

        $users = $query->orderBy('created_at', 'DESC')
            ->paginate(env('PER_PAGE'));

        return $this->responseSuccess($users);
    }

    public function countUser() {
        try {
            $users = User::all()->count();
            return $this->responseSuccess($users);
        } catch (\Exception $e) {
            Log::error('Error get user!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
}
