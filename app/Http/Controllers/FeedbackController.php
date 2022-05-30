<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Feedback\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\Order;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    use ResponseTrait;

    public function index() {
        try {
            $feedbacks = Feedback::orderBy('created_at', 'desc')->with('document')
                ->get();
            return $this->responseSuccess($feedbacks);
        } catch (\Exception $e) {
            Log::error('Error admin get list feedback!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }

    public function store(StoreFeedbackRequest $request) {
        try {
            $order = Order::find($request->input('order_id'));
            if ($order) {
                 $feedback = new Feedback();
                 $feedback->user_id = Auth()->id();
                 $feedback->order_id = $request->input('order_id');
                 $feedback->rate_star = $request->input('rate_star');
                 $feedback->content = $request->input('content');
                 $feedback->save();
            }else {
                return $this->responseError('Order not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update information!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function update(Request $request) {
        try {
            $feedback = Feedback::find($request->input('id'));
            if ($feedback) {
                if ($request->has('rate_star')){
                    $feedback->rate_star = $request->input('rate_star');
                }
                if ($request->has('content')){
                    $feedback->content = $request->input('content');
                }
                 $feedback->save();
            }else {
                return $this->responseError('Feedback not found', [], 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update information!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function getListFeedbacks() {
        try {
            $feedbacks = Feedback::where('user_id', Auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->responseSuccess($feedbacks);
        } catch (\Exception $e) {
            Log::error('Error user get list feedback!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }
}
