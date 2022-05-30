<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;

class FooterController extends Controller
{
    use ResponseTrait;
    public function index()
    {
        try {
            $footer = Footer::first();
            if (!$footer) {
                return $this->responseError('Không có thông tin liên hệ nào.', [], 400);
            }
            return $this->responseSuccess($footer);
        } catch (\Exception $e) {
            Log::error('Error get footer info', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
}
