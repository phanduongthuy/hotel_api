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
            $query = Order::query()->with(['document' => function($q) {
                $q->where('type', '=', Document::TYPE['REQUEST']);
            }, 'languageNative', 'languageTranslate', 'user', 'feedback', 'result']);

            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('file_name', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('code', 'LIKE', "%" . $request->input('q') . "%");
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

            if ($request->has('native_language_id') && strlen($request->input('native_language_id')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('native_language_id', $request->input('native_language_id'));
                });
            }

            if ($request->has('translate_language_id') && strlen($request->input('translate_language_id')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('translate_language_id', $request->input('translate_language_id'));
                });
            }

            if ($request->has('deadline') && strlen($request->input('deadline')) > 0) {
                $query->orderBy('deadline', $request->input('deadline'));
            } else {
                $query->orderBy('created_at', 'DESC');
            }

            $order = $query->paginate(config('constants.per_page'));

            return $this->responseSuccess($order);
        } catch (Exception $e) {
            Log::error('Error get list order!', [
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

    public function destroy($id) {
        try {
            $order = Order::find($id);
            if ($order) {
                if ($order->status == Order::STATUS['NO_PRICE'] || $order->status == Order::STATUS['ALREADY_PRICE']) {
                    $this->destroyDocument($id);
                    $order->delete();
                    return $this->responseSuccess();
                } else {
                    return $this->responseError("You don't delete this order!", [], 404);
                }
            } else {
                return $this->responseError('Order not found', [], 404);
            }

        }catch (Exception $e) {
            Log::error('Error delete order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    private function destroyDocument($id) {
        $document = Document::where('order_id', $id)->first();
        $oldFile = collect(Storage::disk('user_google')->listContents('/', false))
            ->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($document->path['filename'], PATHINFO_FILENAME))
            ->first();
        if ($oldFile) {
            Storage::disk('user_google')->delete($oldFile['path']);
        }
        $document->delete();
    }

    public function countOrder() {
        try {
            $orderAll = Order::all()->count();
            $orderNotQuote = Order::where('status', Order::STATUS['NO_PRICE'])->count();

            $orders = [
                'orderAll'  => $orderAll,
                'orderNotQuote'  => $orderNotQuote
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
            $revenue = Order::whereIn('status', [
                Order::STATUS['TRANSLATION_DONE'],
                Order::STATUS['REVIEW_DONE']
            ])->where('payment_status', Order::PAYMENT_STATUS['PAID'])->sum('total_price');
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

    public function orderStatistics() {
        try {
            $statistics['orderNoPrice'] = Order::where('status', Order::STATUS['NO_PRICE'])->count();
            $statistics['orderTranslating'] = Order::whereIn('status', [
                Order::STATUS['TRANSLATING'],
                Order::STATUS['REVIEWING']
            ])->count();
            $statistics['orderSuccess'] = Order::whereIn('status', [
                Order::STATUS['TRANSLATION_DONE'],
                Order::STATUS['REVIEW_DONE']
            ])
                ->where('payment_status', Order::PAYMENT_STATUS['PAID'])
                ->count();
            return $this->responseSuccess($statistics);
        } catch (Exception $e) {
            Log::error('Error get revenue!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function getOrderNotQuote(Request $request)
    {
        try {
            $query = Order::where('status', Order::STATUS['NO_PRICE'])->with(['document' => function($q) {
                $q->where('type', '=', Document::TYPE['REQUEST']);
            }, 'languageNative', 'languageTranslate', 'user', 'feedback']);

            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('file_name', 'LIKE', "%" . $request->input('q') . "%");
                });
            }

            if ($request->has('type') && strlen($request->input('type')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('type', $request->input('type'));
                });
            }

            if ($request->has('native_language_id') && strlen($request->input('native_language_id')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('native_language_id', $request->input('native_language_id'));
                });
            }

            if ($request->has('translate_language_id') && strlen($request->input('translate_language_id')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('translate_language_id', $request->input('translate_language_id'));
                });
            }

            if ($request->has('deadline') && strlen($request->input('deadline')) > 0) {
                $query->orderBy('deadline', $request->input('deadline'));
            } else {
                $query->orderBy('created_at', 'DESC');
            }

            $order = $query->paginate(config('constants.per_page'));

            return $this->responseSuccess($order);
        } catch (Exception $e) {
            Log::error('Error get list order!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }

    private function sendMailUpdate($user, $file, $order, $emailTemp, $code = null, $linkDowload = null)
    {
        $data['email'] = $user->email;
        $data['customer'] = $user->name != '' ? $user->name : $user->user_name;
        $data['date'] = Carbon::now()->format('d/m/Y');
        $data['type'] = $order->type == Order::TYPE['TRANSLATE'] ? 'Dịch' : 'Review';
        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;
        if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['content']);
        }
        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }

        if ($linkDowload != null) {
            if (str_contains( $data['content'], '{{FILE}}')) {
                $data['content'] = str_ireplace('{{FILE}}', '<a href="'. $linkDowload
                    .'" rel="noopener noreferrer" target="_blank"><strong>' . $file . '</strong></a>', $data['content']);
            }
        } else {
            if (str_contains( $data['content'], '{{FILE}}')) {
                $data['content'] = str_ireplace('{{FILE}}', $file, $data['content']);
            }
        }

        if (str_contains( $data['content'], '{{LOAI}}')) {
            $data['content'] = str_ireplace('{{LOAI}}', $data['type'], $data['content']);
        }
        if (str_contains( $data['content'], '{{THOI_HAN_NHAN_KET_QUA}}')) {
            $data['content'] = str_ireplace('{{THOI_HAN_NHAN_KET_QUA}}', date('d/m/Y',
                $order->deadline), $data['content']);
        }
        if (str_contains( $data['content'], '{{YEU_CAU_CHI_TIET}}')) {
            $data['content'] = str_ireplace('{{YEU_CAU_CHI_TIET}}', $order->note, $data['content']);
        }
        if (str_contains( $data['content'], '{{SO_TRANG}}')) {
            $data['content'] = str_ireplace('{{SO_TRANG}}', number_format($order->total_page), $data['content']);
        }
        if (str_contains( $data['content'], '{{DON_GIA}}')) {
            $data['content'] = str_ireplace('{{DON_GIA}}', number_format($order->price_per_page), $data['content']);
        }
        if (str_contains( $data['content'], '{{TONG_THANH_TIEN}}')) {
            $data['content'] = str_ireplace('{{TONG_THANH_TIEN}}', number_format($order->total_price), $data['content']);
        }

        if ($code != null) {
            if (str_contains( $data['content'], '{{MA_DON_HANG}}')) {
                $data['content'] = str_ireplace('{{MA_DON_HANG}}', $order->code, $data['content']);
            }
            if (str_contains( $data['subject'], '{{MA_DON_HANG}}')) {
                $data['subject'] = str_ireplace('{{MA_DON_HANG}}', $order->code, $data['subject']);
            }
        }

        if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
            $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['subject']);
        }

        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        if (str_contains( $data['subject'], '{{LOAI}}')) {
            $data['subject'] = str_ireplace('{{LOAI}}', $data['type'], $data['subject']);
        }

        return $data;
    }
}
