<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Email\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;

class EmailController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        try {
            $query = EmailTemplateGroup::query();

            if ($request->has('q') && strlen($request->input('q')) > 0 ) {
                $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
            }
            $emailTemplateGroups = $query->with('emailTemplates')
                ->get();

            return $this->responseSuccess($emailTemplateGroups);
        } catch (\Exception $e) {
            Log::error('Error get list email template', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(UpdateEmailTemplateRequest $request, $id)
    {
        try {
            $email = EmailTemplate::find($id);
            $email->subject = $request->input('subject');
            $email->content = $request->input('content');
            $email->save();

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update email template', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }

    public function show($id)
    {
        try {
            $email = EmailTemplate::findOrFail($id);
            if ($email) {
                return $this->responseSuccess($email);
            } else {
                $error = ['email' => ['Email template not found!']];
                return $this->responseError('error', $error, 404);
            }

        } catch (\Exception $e) {
            Log::error('Error get email template', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }
}
