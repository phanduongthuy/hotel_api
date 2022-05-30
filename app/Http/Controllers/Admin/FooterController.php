<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Footer\StoreFooterInfoRequest;
use App\Models\Footer;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    use ResponseTrait;
    public function getFooterInfo()
    {
        try {
            $footer = Footer::first();
            if (!$footer) {
                return $this->responseError('Không có thông tin liên hệ nào.', [], 400);
            }
            return $this->responseSuccess($footer);
        } catch (Exception $e) {
            Log::error('Error get footer info', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function handleCreateAndUpdateFooter (StoreFooterInfoRequest $request)
    {
        try {
            $footer = Footer::first();
            if (!$footer) {
                $footer = new Footer();
            }

            $footer->company = $request->input('company');
            $footer->legal_representation = $request->input('legal_representation');
            $footer->email = $request->input('email');
            $footer->phone = $request->input('phone');
            $footer->address = $request->input('address');
            $footer->business_license = $request->input('business_license');
            $footer->facebook = $request->input('facebook');
            $footer->zalo = $request->input('zalo');
            $footer->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store info footer', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
}
