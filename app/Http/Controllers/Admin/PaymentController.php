<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMomo;
use App\Models\PaymentVNPAY;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ResponseTrait;

    public function listPaymentVnpay(Request $request) {
        $query = PaymentVNPAY::query()->with(['user']);

        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->whereHas('user', function ($qr) use ($request) {
                $qr->where('name', 'LIKE', "%" . $request->input('q') . "%");
            })->orWhere('code', 'LIKE', "%" . $request->input('q') . "%");
        }

        $query = $query->where('status', PaymentVNPAY::STATUS['SUCCESS']);
        $payment = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));


        foreach ($payment as $item) {
            $data = [];
            foreach ($item->order_ids as $id) {
                $order = Order::query()->with(['languageNative', 'languageTranslate', 'feedback', 'document'])->find($id);
                if ($order) {
                    array_push($data, $order);
                }
            }
            $item->orders = $data;
        }

        return $this->responseSuccess($payment);
    }

    public function listPaymentMomo(Request $request) {
        $query = PaymentMomo::query()->with(['user']);

        if ($request->has('q') && strlen($request->input('q')) > 0 ) {
            $query->whereHas('user', function ($qr) use ($request) {
                $qr->where('name', 'LIKE', "%" . $request->input('q') . "%");
            })->orWhere('code', 'LIKE', "%" . $request->input('q') . "%");
        }

        $query = $query->where('status', PaymentMomo::STATUS['SUCCESS']);
        $payment = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

        foreach ($payment as $item) {
            $data = [];
            foreach ($item->order_ids as $id) {
                $order = Order::query()->with(['languageNative', 'languageTranslate', 'feedback', 'document'])->find($id);
                if ($order) {
                    array_push($data, $order);
                }
            }
            $item->orders = $data;
        }

        return $this->responseSuccess($payment);
    }
}
