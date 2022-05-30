<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NotificationPopupGroup;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationPopupController extends Controller
{
    use ResponseTrait;

    public function getNotificationByGroup(Request $request) {
        try {
            $notifications = NotificationPopupGroup::with(['notificationPopups'])
                ->get();

            return $this->responseSuccess($notifications);
        } catch (Exception $e) {
            Log::error('Error get notification by group', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }
}
