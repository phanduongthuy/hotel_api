<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportRequest\StoreSupportRequest;
use App\Mail\MailNotifyRespone;
use App\Models\EmailTemplate;
use App\Models\SupportRequest;
use App\Models\SupportRequestRespone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportRequestController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = SupportRequest::query()->with(['response']);

            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%" . $request->input('q') . "%")
                        ->orWhere('email', 'LIKE', "%" . $request->input('q') . "%");
                });
            }
            if ($request->has('order_type') && strlen($request->input('order_type')) > 0) {
                $query->orderBy('created_at', $request->input('order_type'));
            } else {
                $query->orderBy('created_at','DESC');
            }

            $support_request = $query->paginate(config('constants.per_page'));

            return $this->responseSuccess($support_request);
        } catch (Exception $e) {
            Log::error('Error get list support request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }

    public function store(StoreSupportRequest $request)
    {
        try {
            $support_request = new SupportRequest();
            $support_request->name = $request->input('name');
            $support_request->email = $request->input('email');
            $support_request->content = $request->input('content');
            $support_request->status = SupportRequest::STATUS['PENDING'];
            $support_request->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store support request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $support_request = SupportRequest::findOrFail($id);
            $support_request->status = SupportRequest::STATUS['DONE'];
            $support_request->save();

            SupportRequestRespone::create([
                'request_support_id' => $id,
                'admin_id' => Auth::user()->_id,
                'content' => $request->input('content'),
            ]);

            $emailTemp = EmailTemplate::where('name', 'email-respone-customer')->first();
            $data['content'] = $emailTemp->content;
            $data['subject'] = $emailTemp->subject;

            if (str_contains( $data['content'], '{{NOI_DUNG_PHAN_HOI}}')) {
                $data['content'] = str_ireplace('{{NOI_DUNG_PHAN_HOI}}', $request->input('content'), $data['content']);
            }
            if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
                $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $support_request->name, $data['content']);
            }

            if (str_contains( $data['content'], '{{NGAY}}')) {
                $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
            }

            if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
                $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $support_request->name, $data['subject']);
            }
            if (str_contains( $data['subject'], '{{NGAY}}')) {
                $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
            }

            Mail::to($support_request->email)->send(new MailNotifyRespone($data));
            if (Mail::failures()) {
                Log::error('Error send mail to customer!', [
                    'method' => __METHOD__,
                    'line' => __LINE__,
                    'data' => $request->input('content'),
                ]);
            }

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error change status support request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function amountRequest() {
        try {
            $amount = SupportRequest::where('status', SupportRequest::STATUS['PENDING'])->count();

            return $this->responseSuccess($amount);
        } catch (Exception $e) {
            Log::error('Error count support request!', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
        }
    }
}
