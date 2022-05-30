<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Communication;
use Exception;

class CommunicationController extends Controller
{
    use ResponseTrait;

    public function getCommunation()
    {
        try {
            $communication = Communication::first();
            if (!$communication) {
                return $this->responseError('Không có thông tin liên hệ nào.', [], 400);
            }
            return $this->responseSuccess($communication);
        } catch (Exception $e) {
            Log::error('Error get account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function handleCreateAndUpdateCommunication (Request $request)
    {
        try {
            $communication = Communication::first();
            if (!$communication) {
                $communication = new Communication();
            }

            $communication->facebook = $request->input('facebook');
            $communication->email = $request->input('email');
            $communication->phone = $request->input('phone');
            $communication->twitter = $request->input('twitter');
            $communication->address = $request->input('address');
            $communication->introduce = $request->input('introduce');

            $communication->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
}
