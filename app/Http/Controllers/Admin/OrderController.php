<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\UpdateOrderRequest;
use App\Mail\NotificationAdminUpdateOrder;
use App\Models\Document;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpParser\Comment\Doc;

class OrderController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Order::query();

            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('customer_name', 'LIKE', "%" . $request->input('q') . "%");
                });
            }

            if ($request->has('type') && strlen($request->input('type')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('type', $request->input('type'));
                });
            }

            if ($request->has('status') && strlen($request->input('status')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('status', (int)$request->input('status'));
                });
            }

            $order = $query->paginate(env('PER_PAGE'));

            return $this->responseSuccess($order);
        } catch (Exception $e) {
            Log::error('Error get list order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }

    public function store(Request $request) {
        try {
            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                $user->name = $request->customer_name;
                $user->save();
                $userId = $user->_id;
            } else {
                $userNew = new User();
                $userNew->name = $request->customer_name;
                $userNew->phone = $request->phone;
                $userNew->save();
                $userId = $userNew->_id;
            }

            $order = new Order();
            $order->user_id = $userId;
            $order->customer_name = $request->customer_name;
            $order->type = $request->type == Order::TYPE['OVERNIGHT'] ? Order::TYPE['OVERNIGHT'] : Order::TYPE['HOURS'];
            $order->status = Order::STATUS['REQUEST'];
            $order->order_date = Carbon::now()->timestamp;
            $order->save();

            return $this->responseSuccess();
        }catch (Exception $e) {
            Log::error('Error request order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }



    public function update(UpdateOrderRequest $request) {
        try {
            $order = Order::find($request->input('order_id'));
            $user = User::find($order->user_id);
            if(!$order){
                return $this->responseError('Order not found', [], 404);
            }
            $order->admin_id = Auth()->guard('admins')->user()->_id;

            if($request->has('status')) {
                $order->status = (int)$request->input('status');
            }
            if($request->has('price_per_page')) {
                $order->price_per_page = (int)$request->input('price_per_page');
            }
            if($request->has('total_page')) {
                $order->total_page = (int)$request->input('total_page');
            }
            $order->total_price = $order->price_per_page * $order->total_page;
            if($request->has('payment_status')) {
                $order->payment_status = (int)$request->input('payment_status');
                if ((int)$request->input('payment_status') === 1) {
                    $payment = new Payment();
                    $payment->user_id = $order->user_id;
                    $payment->order_id = $order->_id;
                    $payment->money = $order->price_per_page * $order->total_page;
                    $payment->save();

                    $emailTempUser = EmailTemplate::where('name', 'email-admin-update-confirm-payment')->first();
                    $dataUser = $this->sendMailUpdate($user, substr($order->document->name, 13), $order,
                        $emailTempUser, $order->code);
                    Mail::to($dataUser['email'])->send(new NotificationAdminUpdateOrder($dataUser));
                    if( Mail::failures()) {
                        Log::error('Send fail mail notification update order info', [
                            'method' => __METHOD__,
                            'line' => __LINE__,
                        ]);
                    }
                }
            }
            $order->save();
            if (!$request->input('payment_status')) {
                $linkDowload = env('APP_URL'). '/api/download-file/' . $order->document->_id;
                $emailTempUser = EmailTemplate::where('name', 'email-admin-update-order-info')->first();
                $dataUser = $this->sendMailUpdate($user, substr($order->document->name, 13), $order,
                    $emailTempUser, null,  $linkDowload);
                Mail::to($dataUser['email'])->send(new NotificationAdminUpdateOrder($dataUser));
                if( Mail::failures()) {
                    Log::error('Send fail mail notification update order info', [
                        'method' => __METHOD__,
                        'line' => __LINE__,
                    ]);
                }
            }

            return $this->responseSuccess();
        }catch (Exception $e) {
            Log::error('Error update order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }





    public function countOrder() {
        try {
            $orderAll       = Order::all()->count();
            $orderRequest   = Order::where('status', Order::STATUS['REQUEST'])->count();
            $orderSuccess   = Order::where('status', Order::STATUS['SUCCESS'])->count();
            $orderCancel    = Order::where('status', Order::STATUS['CANCEL'])->count();
            $orders = [
                'orderAll'      =>  $orderAll,
                'orderRequest'  =>  $orderRequest,
                'orderSuccess'  =>  $orderSuccess,
                'orderCancel'   =>  $orderCancel
            ];
            return $this->responseSuccess($orders);
        } catch (Exception $e) {
            Log::error('Error get order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }

    }

    public function getTotalRevenue() {
        try {
            $revenue = Order::where('status', Order::STATUS['SUCCESS'])
                ->where('payment_status', Order::PAYMENT_STATUS['PAID'])
                ->sum('total_price');
            return $this->responseSuccess($revenue);
        } catch (Exception $e) {
            Log::error('Error get revenue!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }


    public function getOrderRequest(Request $request)
    {
        try {
            $query = Order::query();

            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('customer_name', 'LIKE', "%" . $request->input('q') . "%");
                });
            }

            if ($request->has('type') && strlen($request->input('type')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('type', $request->input('type'));
                });
            }


            $order = $query->where('status', Order::STATUS['REQUEST'])
                ->with(['user'])
                ->paginate(env('PER_PAGE'));

            return $this->responseSuccess($order);
        } catch (Exception $e) {
            Log::error('Error get list order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }

    public function confirmOrderRequest($id)
    {
        try {
            $order = Order::find($id);
            if($order) {
                $order->status = Order::STATUS['CONFIRMED'];
                $order->checkin_time = Carbon::now()->timestamp;
                $order->admin_id = Auth()->guard('admins')->user()->_id;;
                $order->save();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Không tìm thấy đơn hàng!', [], 404);
            }

        } catch (Exception $e) {
            Log::error('Error confirm request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
    public function cancelOrderRequest($id)
    {
        try {
            $order = Order::find($id);
            if($order) {
                $order->status = Order::STATUS['CANCEL'];
                $order->save();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Không tìm thấy đơn hàng!', [], 404);
            }

        } catch (Exception $e) {
            Log::error('Error cancel request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

}
