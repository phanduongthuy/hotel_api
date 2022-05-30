<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Verify\ResultVerifyEmail;
use App\Http\Requests\Admin\Verify\VerifyEmailRequest;
use App\Mail\VerifyEmail;
use App\Models\AuthenticationEmailCode;
use App\Models\EmailTemplate;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    use ResponseTrait;

    public function verifyEmail(VerifyEmailRequest $request) {

        try {
            $verify = new AuthenticationEmailCode();
            $verify->email = $request->input('email');
            $verify->code = rand(100000, 999999);
            $verify->expired = Carbon::now()->addDay(1)->timestamp;
            $verify->save();

            $data['email'] = $verify->email;
            $data['code'] = $verify->code;

            $this->sendEmail($data, $verify->email);

            return $this->responseSuccess();
        }catch (\Exception $e) {
            Log::error('Error get list language', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }

    }

    public function resultVerifyEmail(ResultVerifyEmail $request) {
        try {
            $result = false;
            $verify = AuthenticationEmailCode::where('email', $request->input('email'))
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$verify) {
                $error = ['email' => ['Error email!']];
                return $this->responseError('error', $error, 400);
            }else {
                $expired_time = $verify->expired - Carbon::now()->timestamp;
                if($verify->code == $request->input('code')) {
                    $result = true;
                }else {
                    $result = false;
                    $error = ['code' => ['Mã xác thực không hợp lệ!']];
                    return $this->responseError('error', [$result, $error], 400);
                }

                if($expired_time > 0) {
                    $result = true;
                }else {
                    $result = false;
                    $error = ['code' => ['Mã xác thực đã hết hạn!']];
                    return $this->responseError('error', [$result, $error], 400);
                }

                return $this->responseSuccess($result);
            }

        }catch (\Exception $e) {
            Log::error('Error get list language', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    private function sendEmail($data, $email)
    {
        $emailTemp = EmailTemplate::where('name', 'email-verify')->first();

        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;

        if (str_contains( $data['content'], '{{MA_XAC_THUC}}')) {
            $data['content'] = str_ireplace('{{MA_XAC_THUC}}', $data['code'], $data['content']);
        }

        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }

        if (str_contains( $data['content'], '{{EMAIL}}')) {
            $data['content'] = str_ireplace('{{EMAIL}}', $email, $data['content']);
        }

        if (str_contains( $data['subject'], '{{EMAIL}}')) {
            $data['subject'] = str_ireplace('{{EMAIL}}', $email, $data['subject']);
        }

        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        Mail::to($email)->send(new VerifyEmail($data));

        if( Mail::failures()) {
            Log::error('Error send mail verify Email', [
                'method' => __METHOD__,
                'line' => __LINE__,
            ]);
        }
    }
}
