<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\NotificatonPaymentPaypal;
use App\Models\Document;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\PaymentCOD;
use App\Models\PaymentMomo;
use App\Models\PaymentPayPal;
use App\Models\PaymentVNPAY;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NotifiPaymentCash;

class PaymentController extends Controller
{
    use ResponseTrait;

    private $PREFIX_CODE_MOMO = "MMTF";
    private $PREFIX_CODE_VNPAY = "VNPTF";
    private $PREFIX_CODE_COD = "CODTF";
    private $PREFIX_CODE_PAYPAL = "PAYPALTF";

    public function store(Request $request)
    {
        try {
            if (!$request->has('payment_type')) {
                return response()->json(['error' => 'Please choose payment type'], 404);
            }
            if ($request->has('payment_type') == Order::PAYMENT_TYPE['PAYMENT_WITH_VNPAY']) {
                $vnp_TxnRef = $this->getOrderIdCode('VNPAY');
                $vnp_OrderInfo = 'Thanh toán đơn hàng qua ví VNPAY';
                $vnp_OrderType = 'other';
                $vnp_Amount = Order::whereIn('_id', $request->order_ids)->get()->sum('total_price') * 100;
                $vnp_Locale = 'vi';
                $vnp_BankCode = $request->input('bank_code');
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

                $inputData = [
                    "vnp_Version" => "2.0.1",
                    "vnp_TmnCode" => env('VNP_TMN_CODE'),
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => env('VNP_RETURN_URL'),
                    "vnp_TxnRef" => $vnp_TxnRef,
                ];

                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }

                $this->storePaymentVnpay($inputData, $request->input('order_ids'));

                $data['order_bill_type'] = $request->input('order_bill_type');
                $data['company_name'] = $request->input('company_name');
                $data['company_address'] = $request->input('company_address');
                $data['tax_code'] = $request->input('tax_code');
                $this->updateOrder($data, $request->input('order_ids'));

                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . $key . "=" . $value;
                    } else {
                        $hashdata .= $key . "=" . $value;
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = env('VNP_URL') . "?" . $query;
                if (env('VNP_HASHSECRET')) {
                    $vnpSecureHash = hash('sha256', env('VNP_HASHSECRET') . $hashdata);
                    $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
                }
                return $this->responseSuccess($vnp_Url);
            }
            return response()->json(['error' => 'Payment type invalid'], 404);

        } catch (\Exception $e) {
            Log::error('Error payment', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }

    }

    public function storePaymentVnpay($data, $orderIds) {
        $payment = new PaymentVNPAY();
        $payment->user_id = Auth()->id();
        $payment->order_ids = $orderIds;
        $payment->code = $data['vnp_TxnRef'];
        $payment->money = $data['vnp_Amount'] / 100;
        $payment->content = $data['vnp_OrderInfo'];
        $payment->status = PaymentVNPAY::STATUS['UNPAID'];
        $payment->code_bank = $data['vnp_BankCode'];
        $payment->time = null;
        $payment->save();
    }

    public function paymentSuccess(Request $request)
    {
        try {
            $payment = PaymentVNPAY::where('code', $request->code)->first();
            $payment->status = PaymentVNPAY::STATUS['SUCCESS'];
            $payment->time = $request->time;
            $payment->save();

            foreach ($request->input('order_ids') as $order_id) {
                $order = Order::find($order_id);
                $order->payment_status = Order::PAYMENT_STATUS['PAID'];
                $order->payment_type = Order::PAYMENT_TYPE['PAYMENT_WITH_VNPAY'];
                $order->code = $payment->code;
                $order->save();
            }
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error payment', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    public function paymentMomo(Request $request)
    {
        try {
            $money = Order::whereIn('_id', $request->order_ids)->get()->sum('total_price');

            while (true) {
                $requestId = Str::uuid()->toString();
                $data = PaymentMomo::where('request_id', $requestId)->first();
                if (!$data) break;
            }
            $partnerCode = env('MOMO_PARTNER_CODE');
            $partnerName = env('MOMO_PARTNER_NAME');
            $storeId = env('MOMO_STORE_ID');            // Mã cửa hàng
            $requestType = 'captureWallet';
            $ipnUrl = env('MOMO_IPN_URL');              // url momo trả về server để xử lý sau khi khách hàng thanh toán
            $redirectUrl = env('MOMO_REDIRECT_URL');    // url trở về sau khi khách hàng thanh toán
            $amount = $money;                                // Tổng số tiền
            $lang = 'vi';
            $autoCapture = false;
            $orderInfo = 'Thanh toán đơn hàng qua ví MoMo';  // Nội dung thanh toán
            $orderId = $this->getOrderIdCode('MOMO');             // Mã đơn hàng
            $verificationCode = Str::random(10) . $orderId;
            $extraData = Crypt::encryptString($verificationCode);;
            $accessKey = env('MOMO_ACCESS_KEY');
            $secretKey = env('MOMO_SECRET_KEY');
            $signature = hash_hmac('sha256', 'accessKey='.$accessKey.'&amount='.$amount.
                '&extraData='.$extraData. '&ipnUrl='.$ipnUrl.'&orderId='.$orderId.'&orderInfo='.$orderInfo.
                '&partnerCode='.$partnerCode. '&redirectUrl='.$redirectUrl.'&requestId='.$requestId.
                '&requestType='.$requestType, $secretKey);

            $header = ['Content-Type'  => 'application/json'];
            $data = [
                "partnerCode"   => $partnerCode,
                "partnerName"   => $partnerName,
                "storeId"       => $storeId,
                "requestType"   => $requestType,
                "ipnUrl"        => $ipnUrl,
                "redirectUrl"   => $redirectUrl,
                "orderId"       => $orderId,
                "amount"        => $amount,
                "lang"          => $lang,
                "autoCapture"   => $autoCapture,
                "orderInfo"     => $orderInfo,
                "requestId"     => $requestId,
                "extraData"     => $extraData,
                "signature"     => $signature
            ];

            $this->storePaymentMomo($data, $request->order_ids, $verificationCode);

            $data['order_bill_type'] = $request->input('order_bill_type');
            $data['company_name'] = $request->input('company_name');
            $data['company_address'] = $request->input('company_address');
            $data['tax_code'] = $request->input('tax_code');
            $this->updateOrder($data, $request->input('order_ids'));

            $payload = json_encode($data);
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', env('MOMO_URL_CREATE_PAYMENT'), [
                'headers'   => $header,
                'body'      => $payload
            ])->getBody()->getContents();
            $response = json_decode($response, true);

            if ($response['resultCode'] == 0) {
                return response()->json($response);
            }
            return $this->responseError('Error payment momo');
        } catch (Exception $e) {
            Log::error('Error payment momo', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    private function storePaymentMomo($data, $orderIds, $verificationCode) {
        $payment = new PaymentMomo();
        $payment->user_id = Auth()->id();
        $payment->order_ids = $orderIds;
        $payment->code = $data['orderId'];
        $payment->money = $data['amount'];
        $payment->content = 'Thanh toán đơn hàng qua ví MoMo';
        $payment->request_id = $data['requestId'];
        $payment->status = PaymentMomo::STATUS['UNPAID'];
        $payment->trans_id = null;
        $payment->time = Carbon::now()->timestamp;
        $payment->verification_code = $verificationCode;
        $payment->save();
    }

    public function paymentMomoConfirm(Request $request) {
        try {
            $data = $request->all();
            $payment = PaymentMomo::where('verification_code', Crypt::decryptString($data['extraData']))-> first();

            if ($payment) {
                $partnerCode = env('MOMO_PARTNER_CODE');
                $requestId = $data['requestId'];
                $orderId = $data['orderId'];
                $requestType = ($data['resultCode'] == 9000) ? 'capture' : 'cancel';
                $lang = 'vi';
                $amount = $data['amount'];
                $description = '';
                $accessKey = env('MOMO_ACCESS_KEY');
                $secretKey = env('MOMO_SECRET_KEY');
                $signature = hash_hmac('sha256', 'accessKey='.$accessKey.'&amount='.$amount.
                    '&description='.$description. '&orderId='.$orderId.'&partnerCode='.$partnerCode.
                    '&requestId='.$requestId.'&requestType='.$requestType, $secretKey);

                $header = ['Content-Type'  => 'application/json'];
                $data = [
                    "partnerCode"   => $partnerCode,
                    "requestId"     => $requestId,
                    "orderId"       => $orderId,
                    "requestType"   => $requestType,
                    "lang"          => $lang,
                    "amount"        => $amount,
                    "description"   => $description,
                    "signature"     => $signature
                ];
                $payload = json_encode($data);

                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', env('MOMO_URL_CONFIRM_PAYMENT'), [
                    'headers'   => $header,
                    'body'      => $payload
                ])->getBody()->getContents();

                $response = json_decode($response, true);

                $payment->status = ($response['resultCode'] == 0 && $response['requestType'] == 'capture') ? PaymentMomo::STATUS['SUCCESS'] : PaymentMomo::STATUS['FAILURE'];
                $payment->trans_id = $response['transId'];
                $payment->time = $response['responseTime'];
                $payment->save();

                if ($payment->status == PaymentMomo::STATUS['SUCCESS']) {
                    foreach ($payment->order_ids as $id) {
                        $order = Order::find($id);
                        $order->payment_status = Order::PAYMENT_STATUS['PAID'];
                        $order->payment_type = Order::PAYMENT_TYPE['PAYMENT_WITH_MOMO'];
                        $order->code = $payment->code;
                        $order->save();
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Error confirm payment momo', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
            return $this->responseError();
        }
    }

    public function handleCashPayment(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$request->has('payment_type')) {
                return response()->json(['error' => 'Please choose payment type'], 404);
            }
            $code = '';
            if ((int)$request->input('payment_type') === Order::PAYMENT_TYPE['PAYMENT_ON_DELIVERY']) {
                if ($request->has('order_ids') && is_array($request->input('order_ids'))) {
                    $orders = Order::where('user_id', $user->_id)
                        ->whereIn('_id', $request->order_ids)
                        ->where('payment_status', Order::PAYMENT_STATUS['UNPAID'])
                        ->where('status','>', Order::STATUS['NO_PRICE'])
                        ->get();
                    $arrOrderIds = $orders->map(function ($item) {
                        return $item->_id;
                    });
                    if (count($arrOrderIds) !== 0) {
                        $totalMoney = Order::whereIn('_id', $arrOrderIds)->get()->sum('total_price');
                        $payment = new PaymentCOD();
                        $payment->user_id = Auth()->id();
                        $payment->order_ids = $arrOrderIds;
                        $payment->code = $this->getOrderIdCode('COD');
                        $payment->money = $totalMoney;
                        $payment->status = PaymentCOD::STATUS['UNPAID'];
                        $payment->time = null;
                        $payment->save();

                        foreach ($arrOrderIds as $orderId) {
                            $order = Order::find($orderId);
                            if ($order->status !== Order::STATUS['NO_PRICE']) {
                                $order->update([
                                    'payment_type' => Order::PAYMENT_TYPE['PAYMENT_ON_DELIVERY'],
                                    'payment_status' => Order::PAYMENT_STATUS['WAiTING_PAYMENT'],
                                    'code'  => $payment->code
                                ]);
                            };
                        }

                        $data['order_bill_type'] = $request->input('order_bill_type');
                        $data['company_name'] = $request->input('company_name');
                        $data['company_address'] = $request->input('company_address');
                        $data['tax_code'] = $request->input('tax_code');
                        $this->updateOrder($data, $request->input('order_ids'));

                        $data['email'] = $user->user_name;
                        $data['name'] = $user->name;
                        $data['total_money'] = $request->input('total_money');
                        $data['date'] = \Illuminate\Support\Carbon::now()->format('d/m/Y');
                        $data['account_holder'] = $request->input('account_holder');
                        $data['account_number'] = $request->input('account_number');
                        $data['bank_name'] = $request->input('bank_name');
                        $data['note'] = $request->input('note');
                        $data['orders'] = $request->input('orders');
                        $data['code'] = $payment->code;

                        $this->sendMailInfoCash($data);
                    } else {
                        return response()->json(['error' => 'Đơn hàng này đang chờ được thanh toán'], 400);
                    }
                }
                return $this->responseSuccess();
            }
            return response()->json(['error' => 'Payment type invalid'], 404);

        } catch (\Exception $e) {
            Log::error('Error payment', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }

    }

    public function handlePaypalPayment(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$request->has('payment_type')) {
                return response()->json(['error' => 'Bạn chưa chọn phương thức thanh toán'], 404);
            }
            if ((int)$request->input('payment_type') === Order::PAYMENT_TYPE['PAYMENT_WITH_PAYPAL']) {
                if ($request->has('order_ids') && is_array($request->input('order_ids'))) {
                    $orders = Order::where('user_id', $user->_id)
                        ->whereIn('_id', $request->order_ids)
                        ->where('payment_status', Order::PAYMENT_STATUS['UNPAID'])
                        ->where('status','>', Order::STATUS['NO_PRICE'])
                        ->get();
                    $arrOrderIds = $orders->map(function ($item) {
                        return $item->_id;
                    });
                    if (count($arrOrderIds) !== 0) {
                        $totalMoney = Order::whereIn('_id', $arrOrderIds)->get()->sum('total_price');
                        $payment = new PaymentPayPal();
                        $payment->user_id = Auth()->id();
                        $payment->order_ids = $arrOrderIds;
                        $payment->code = $this->getOrderIdCode('PAYPAL');
                        $payment->money = $totalMoney;
                        $payment->status = PaymentPayPal::STATUS['UNPAID'];
                        $payment->time = null;
                        $payment->save();

                        foreach ($arrOrderIds as $orderId) {
                            $order = Order::find($orderId);
                            if ($order->status !== Order::STATUS['NO_PRICE']) {
                                $order->update([
                                    'payment_type' => Order::PAYMENT_TYPE['PAYMENT_WITH_PAYPAL'],
                                    'payment_status' => Order::PAYMENT_STATUS['WAiTING_PAYMENT'],
                                    'code'  => $payment->code
                                ]);
                            };
                        }

                        $data['order_bill_type'] = $request->input('order_bill_type');
                        $data['company_name'] = $request->input('company_name');
                        $data['company_address'] = $request->input('company_address');
                        $data['tax_code'] = $request->input('tax_code');
                        $this->updateOrder($data, $request->input('order_ids'));

                        $this->sendMailInfoPaypal($request->input('name'), $request->input('email'));
                    } else {
                        return response()->json(['error' => 'Đơn hàng này đang chờ được thanh toán'], 400);
                    }
                }
                return $this->responseSuccess($data);
            }
            return response()->json(['error' => 'Payment type invalid'], 404);

        } catch (\Exception $e) {
            Log::error('Error payment', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }

    }

    public function getOrderIdCode($flag) {
        if ($flag === "MOMO") {
            $payment = PaymentMomo::orderBy('created_at','DESC')->first();
            if (empty($payment)) {
                $code = $this->PREFIX_CODE_MOMO . '000000001';
            } else {
                $code = (int) substr($payment->code, 4) +1;
                return $this->PREFIX_CODE_MOMO . str_pad($code, 9, '0', STR_PAD_LEFT);
            }
        } elseif ($flag === "VNPAY") {
            $payment = PaymentVNPAY::orderBy('created_at','DESC')->first();
            if (empty($payment)) {
                $code = $this->PREFIX_CODE_VNPAY . '000000001';
            } else {
                $code = (int) substr($payment->code, 5) +1;
                return $this->PREFIX_CODE_VNPAY . str_pad($code, 9, '0', STR_PAD_LEFT);
            }
        } elseif ($flag === "PAYPAL") {
            $payment = PaymentPayPal::orderBy('created_at','DESC')->first();
            if (empty($payment)) {
                $code = $this->PREFIX_CODE_PAYPAL . '000000001';
            } else {
                $code = (int) substr($payment->code, 5) +1;
                return $this->PREFIX_CODE_PAYPAL . str_pad($code, 9, '0', STR_PAD_LEFT);
            }
        } else {
            $payment = PaymentCOD::orderBy('created_at','DESC')->first();
            if (empty($payment)) {
                $code = $this->PREFIX_CODE_COD . '000000001';
            } else {
                $code = (int) substr($payment->code, 5) +1;
                return $this->PREFIX_CODE_COD . str_pad($code, 9, '0', STR_PAD_LEFT);
            }
        }

        return $code;
    }

    private function updateOrder($data, $orderIds) {
        foreach ($orderIds as $order_id) {
            $order = Order::find($order_id);
            if ((int)$data['order_bill_type'] == Order::ORDER_BILL_TYPE['NO_BILL']) {
                $order->order_bill_type = Order::ORDER_BILL_TYPE['NO_BILL'];
            } else {
                $order->order_bill_type = Order::ORDER_BILL_TYPE['VAT_BILL'];
                $order->company_name = $data['company_name'];
                $order->company_address = $data['company_address'];
                $order->tax_code = $data['tax_code'];
            }
            $order->save();
        }
    }

    private function sendMailInfoPaypal($name, $email)
    {
        $emailTemp = EmailTemplate::where('name', 'email-payment-with-PayPal-info')->first();
        $data['email'] = $email;
        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;
        if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $name, $data['content']);
        }
        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }
        if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
            $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $name, $data['subject']);
        }
        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        Mail::to($email)->send(new NotificatonPaymentPaypal($data));
        if( Mail::failures()) {
            Log::error('Send fail mail notification payment', [
                'method' => __METHOD__,
                'line' => __LINE__,
            ]);
        }
    }

    private function sendMailInfoCash($data)
    {
        $emailTemp = EmailTemplate::where('name', 'email-payment-with-cash-info')->first();
        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;

        if (str_contains( $data['content'], '{{TEN_NGAN_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_NGAN_HANG}}', $data['bank_name'], $data['content']);
        }

        if (str_contains( $data['content'], '{{SO_TAI_KHOAN}}')) {
            $data['content'] = str_ireplace('{{SO_TAI_KHOAN}}', $data['account_number'], $data['content']);
        }
        if (str_contains( $data['content'], '{{CHU_TAI_KHOAN}}')) {
            $data['content'] = str_ireplace('{{CHU_TAI_KHOAN}}', $data['account_holder'], $data['content']);
        }
        if (str_contains( $data['content'], '{{TONG_TIEN}}')) {
            $data['content'] = str_ireplace('{{TONG_TIEN}}', $data['total_money'], $data['content']);
        }
        if (str_contains( $data['content'], '{{NOI_DUNG_CHUYEN_KHOAN}}')) {
            $data['content'] = str_ireplace('{{NOI_DUNG_CHUYEN_KHOAN}}', $data['note'], $data['content']);
        }
        if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $data['name'], $data['content']);
        }

        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }
        if (str_contains( $data['content'], '{{MA_DON_HANG}}')) {
            $data['content'] = str_ireplace('{{MA_DON_HANG}}', $data['code'], $data['content']);
        }

        if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
            $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $data['name'], $data['subject']);
        }
        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        if (str_contains( $data['subject'], '{{MA_DON_HANG}}')) {
            $data['subject'] = str_ireplace('{{MA_DON_HANG}}', $data['code'], $data['subject']);
        }

        Mail::to($data['email'])->send(new NotifiPaymentCash($data));

        if( Mail::failures()) {
            Log::error('Send fail mail notification payment', [
                'method' => __METHOD__,
                'line' => __LINE__,
            ]);
        }
    }
}
