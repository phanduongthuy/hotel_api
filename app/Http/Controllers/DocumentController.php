<?php

namespace App\Http\Controllers;

use App\Mail\NotificationAdminUpdateOrder;
use App\Mail\NotificationAdminUpdateResultOrder;
use App\Mail\NotificationRequestFile;
use App\Mail\NotificationRequestFileToAdmin;
use App\Mail\NotificationUpdateFileToAdmin;
use App\Models\Document;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationUpdateFile;

class DocumentController extends Controller
{
    use ResponseTrait;

    /**
     * Upload document to Google Drive.
     *
     * @return \Illuminate\Http\Response
     */
    public function userUpload(Request $request)
    {
        try {
            $userId = '';
            $userCheck = auth()->guard('users')->check();
            if (!$userCheck) {
                if ($request->input('email')) {
                    if ($this->isExistEmail($request->input('email'))) {
                        $user = User::where('email', $request->input('email'))->first();
                        $user->name = $request->input('name');
                        $user->phone = $request->input('phone');
                        $user->password = Hash::make($request->input('password'));
                        $user->save();
                        $userId = $user->_id;
                    } else {
                        $user = new User();
                        $user->name = $request->input('name');
                        $user->phone = $request->input('phone');
                        $user->email = $request->input('email');
                        $user->user_name = $request->input('email');
                        $user->password = Hash::make($request->input('password'));
                        $user->save();
                        $userId = $user->_id;
                    }
                }
                $credentials = request(['email', 'password']);
                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else {
                    $userCheck = true;
                }
            }

            if ($userCheck) {
                if ($request->hasFile('documents')) {
                    $files = $request->file('documents');
                    $index = 0;
                    $data['file'] = '';
                    $dataFiles = [];
                    foreach ($files as $file) {
                        $filename = date('ymdhis') . '_' . $files[$index]->getClientOriginalName();
                        $data['file'] .= $files[$index]->getClientOriginalName() . ', ';
                        $fileData = File::get($files[$index]);
                        Storage::disk('user_google')->put($filename, $fileData);

                        $contents = collect(Storage::disk('user_google')->listContents('/', false))
                            ->where('type', '=', 'file')
                            ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                            ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                            ->first;
                        $order = new Order();
                        if ($userId == '') {
                            $order->user_id = Auth::id();
                        } else {
                            $order->user_id = $userId;
                        }
                        $order->file_name = $filename;
                        $order->status = Order::STATUS['NO_PRICE'];
                        $order->type = $request->input('type');
                        $order->native_language_id = $request->input('native_language_id');
                        if ($request->input('type') == Order::TYPE['TRANSLATE']) {
                            $order->translate_language_id = $request->input('translate_language_id');
                        } else {
                            $order->translate_language_id = null;
                        }
                        $order->deadline = $request->input('deadline');
                        $order->return_date = null;
                        $order->admin_id = null;
                        $order->note = $request->input('note');
                        $order->payment_status = Order::PAYMENT_STATUS['UNPAID'];
                        $order->payment_type = null;
                        $order->total_page = 0;
                        $order->price_per_page = 0;
                        $order->total_price = 0;
                        $order->code = null;
                        $order->save();

                        $document = new Document();
                        $document->order_id = $order->_id;
                        $document->name = $filename;
                        $document->path = $contents->path;
                        $document->total_page = 0;
                        $document->type = Document::TYPE['REQUEST'];
                        $document->save();

                        array_push($dataFiles, [
                            'name' => $files[$index]->getClientOriginalName(),
                            'url' => env('APP_URL'). '/api/download-file/' . $document->_id
                        ]);
                        $index++;

                    }

                    $emailTempUser = EmailTemplate::where('name', 'email-request-translate-for-user')->first();
                    $emailTempAdmin = EmailTemplate::where('name', 'email-request-translate-for-admin')->first();
                    $dataUser = $this->sendMailUpload($userId, $request->type, $dataFiles, $emailTempUser,
                        $request->input('deadline'), $request->input('note'));
                    $dataAdmin = $this->sendMailUpload($userId, $request->type, $dataFiles, $emailTempAdmin,
                        $request->input('deadline'), $request->input('note'));

                    Mail::to($dataUser['email'])->send(new NotificationRequestFile($dataUser));
                    Mail::to(env('MAIL_TO_ADMIN'))->send(new NotificationRequestFileToAdmin($dataAdmin));
                    if( Mail::failures()) {
                        Log::error('Send fail mail notification request file', [
                            'method' => __METHOD__,
                            'line' => __LINE__,
                        ]);
                    }
                } else {
                    $error = ['documents' => ['Trường file không được bỏ trống!']];
                    return $this->responseError('error', $error, 400);
                }
                return $this->responseSuccess();
            }

        } catch (Exception $e) {
            Log::error('Error upload files', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    public function adminUpload(Request $request)
    {
        try {
            if ($request->hasFile('document')) {
                $fileReturn = $request->file('document');
                $filename = date('ymdhis') . '_' . $fileReturn->getClientOriginalName();
                $fileData = File::get($fileReturn);
                Storage::disk('admin_google')->put($filename, $fileData);

                $contents = collect(Storage::disk('admin_google')->listContents('/', false))
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                    ->first;

                if (!$request->input('order_id')) {
                    $error = ['order_id' => ['Không có đơn hàng!']];
                    return $this->responseError('error', $error, 400);
                } else {
                    $order = Order::find($request->input('order_id'));
                    if (!$order) {
                        return $this->responseError('Order not found', [], 404);
                    }
                    $order->status = ($order->type == 0) ? Order::STATUS['TRANSLATION_DONE'] : Order::STATUS['REVIEW_DONE'];
                    $order->return_date = Carbon::now()->timestamp;
                    $order->admin_id = Auth()->guard('admins')->user()->_id;
                    $order->save();
                }

                $doccument = new Document();
                $doccument->order_id = $order->_id;
                $doccument->name = $filename;
                $doccument->path = $contents->path;
                $doccument->total_page = $order->total_page;
                $doccument->type = Document::TYPE['RESULT'];
                $doccument->save();

                $user = User::find($order->user_id);
                $linkDowloadFile = env('APP_URL'). '/api/download-file/' . $order->document->_id;
                $linkDowloadFileResult = env('APP_URL'). '/api/download-file/' . $doccument->_id;
                $emailTempUser = EmailTemplate::where('name', 'email-admin-update-order-result')->first();
                $dataUser = $this->sendMailUpdateFileResult($user, $order, substr($order->document->name, 13),
                    $fileReturn->getClientOriginalName(), $emailTempUser, $linkDowloadFile, $linkDowloadFileResult);
                Mail::to($dataUser['email'])->send(new NotificationAdminUpdateResultOrder($dataUser));
                if( Mail::failures()) {
                    Log::error('Send fail mail notification update file result order', [
                        'method' => __METHOD__,
                        'line' => __LINE__,
                    ]);
                }
                return $this->responseSuccess();
            } else {
                $error = ['document' => ['Trường file không được bỏ trống!']];
                return $this->responseError('error', $error, 400);
            }

        } catch (Exception $e) {
            Log::error('Error upload document', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    private function isExistEmail($email)
    {
        $count = User::where('email', $email)->count();
        return $count > 0;
    }

    public function download($id)
    {
        try {
            $document = Document::find($id);
            if ($document) {
                if ($document->type == Document::TYPE['REQUEST']) {
                    $contents = collect(Storage::disk('user_google')->listContents('/', false));
                } else {
                    $contents = collect(Storage::disk('admin_google')->listContents('/', false));
                }

                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($document->name, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($document->name, PATHINFO_EXTENSION))
                    ->first();

                if ($document->type == Document::TYPE['REQUEST']) {
                    $rawData = Storage::disk('user_google')->get($file['path']);
                } else {
                    $rawData = Storage::disk('admin_google')->get($file['path']);
                }

                return response($rawData, 200)
                    ->header('Content-Type', $file['mimetype'])
                    ->header('Content-Disposition', "attachment; filename='$document->name'");

            } else {
                return $this->responseError('Document not found', [], 404);
            }
        } catch (Exception $e) {
            Log::error('Error download file', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    public function updateDocument(Request $request) {
        try{
            $user = Auth::user();
            $document = Document::find($request->input('document_id'));
            if ($document) {
                $data['old_file'] = substr($document->name, 13);
                $order = Order::find($document->order_id);
                if ($order) {
                    if ($order->status == Order::STATUS['NO_PRICE'] || $order->status == Order::STATUS['ALREADY_PRICE']) {
                        $oldFile = collect(Storage::disk('user_google')->listContents('/', false))
                            ->where('type', '=', 'file')
                            ->where('filename', '=', pathinfo($document->path['filename'], PATHINFO_FILENAME))
                            ->first();
                        if ($oldFile) {
                            Storage::disk('user_google')->delete($oldFile['path']);
                        }
                        if ($request->hasFile('document')) {
                            $fileUpdate = $request->file('document');
                            $filename = date('ymdhis') . '_' . $fileUpdate->getClientOriginalName();
                            $fileData = File::get($fileUpdate);
                            Storage::disk('user_google')->put($filename, $fileData);

                            $contents = collect(Storage::disk('user_google')->listContents('/', false))
                                ->where('type', '=', 'file')
                                ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                                ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                                ->first;

                            $document->name = $filename;
                            $document->path = $contents->path;
                            $document->save();

                            $order->file_name = $filename;
                            $order->payment_status = Order::PAYMENT_STATUS['UNPAID'];
                            $order->payment_type = null;
                            $order->total_page = 0;
                            $order->price_per_page = 0;
                            $order->total_price = 0;
                            $order->code = null;
                            $order->save();

                            $data['new_file'] = $fileUpdate->getClientOriginalName();
                            $linkDowloadNewFile = env('APP_URL'). '/api/download-file/' . $document->_id;

                            $emailTempUser = EmailTemplate::where('name', 'email-update-file-for-user')->first();
                            $emailTempAdmin = EmailTemplate::where('name', 'email-update-file-for-admin')->first();
                            $dataUser = $this->sendMailUpdate($user, $data['old_file'], $data['new_file'],
                                $emailTempUser, $linkDowloadNewFile, $order->deadline, $order->note, $order->type);
                            $dataAdmin = $this->sendMailUpdate($user, $data['old_file'], $data['new_file'],
                                $emailTempAdmin, $linkDowloadNewFile, $order->deadline, $order->note, $order->type);

                            Mail::to($dataUser['email'])->send(new NotificationUpdateFile($dataUser));
                            Mail::to(env('MAIL_TO_ADMIN'))->send(new NotificationUpdateFileToAdmin($dataAdmin));

                            if( Mail::failures()) {
                                Log::error('Send fail mail notification update file', [
                                    'method' => __METHOD__,
                                    'line' => __LINE__,
                                ]);
                            }

                            return $this->responseSuccess();
                        } else {
                            $error = ['document' => ['Document required!']];
                            return $this->responseError('error', $error, 400);
                        }
                    } else {
                        return $this->responseError("You don't update this order!", [], 500);
                    }
                } else {
                    return $this->responseError("Order not found!", [], 404);
                }
            } else {
                return $this->responseError('Document not found', [], 404);
            }

        } catch (Exception $e) {
            Log::error('Error upload document', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    private function sendMailUpload($userId, $type, $files, $emailTemp, $deadline = null, $note = null)
    {
        if ($userId == '') {
            $user = User::find(Auth::id());
        } else {
            $user = User::find($userId);
        }
        $data['email'] = $user->email;
        $data['customer'] = $user->name != '' ? $user->name : $user->user_name;
        $data['type'] = $type == Order::TYPE['TRANSLATE'] ? 'Dịch' : 'Review';
        $data['date'] = \Illuminate\Support\Carbon::now()->format('d/m/Y');

        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;
        if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['content']);
        }
        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }
        if (str_contains( $data['content'], '{{LOAI}}')) {
            $data['content'] = str_ireplace('{{LOAI}}', $data['type'], $data['content']);
        }
        $stringFile = '';
        foreach ($files as $index => $file) {
            $stringFile .= '<a href="'. $file['url'] .'" rel="noopener noreferrer" target="_blank"><strong>' . $file['name'] . '</strong></a>';
            if (($index + 1) !== count($files)) {
                $stringFile .= ', ';
            }
        }
        if (str_contains( $data['content'], '{{FILE}}')) {
            $data['content'] = str_ireplace('{{FILE}}', $stringFile, $data['content']);
        }

        if (str_contains( $data['content'], '{{THOI_HAN_NHAN_KET_QUA}}')) {
            $data['content'] = str_ireplace('{{THOI_HAN_NHAN_KET_QUA}}', date('d/m/Y', $deadline), $data['content']);
        }
        if (str_contains( $data['content'], '{{YEU_CAU_CHI_TIET}}')) {
            $data['content'] = str_ireplace('{{YEU_CAU_CHI_TIET}}', $note, $data['content']);
        }

        if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
            $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['subject']);
        }

        if (str_contains( $data['subject'], '{{LOAI}}')) {
            $data['subject'] = str_ireplace('{{LOAI}}', $data['type'], $data['subject']);
        }
        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        return $data;
    }

    private function sendMailUpdate($user, $oldFile, $newFile, $emailTemp, $linkDowloadNewFile, $deadline, $note, $type)
    {
        $data['email'] = $user->email;
        $data['customer'] = $user->name != '' ? $user->name : $user->user_name;
        $data['date'] = Carbon::now()->format('d/m/Y');
        $data['content'] = $emailTemp->content;
        $data['subject'] = $emailTemp->subject;
        if (str_contains( $data['content'], '{{TEN_KHACH_HANG}}')) {
            $data['content'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['content']);
        }
        if (str_contains( $data['content'], '{{NGAY}}')) {
            $data['content'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['content']);
        }

        if (str_contains( $data['content'], '{{FILE_CU}}')) {
            $data['content'] = str_ireplace('{{FILE_CU}}', $oldFile, $data['content']);
        }
        if (str_contains( $data['content'], '{{FILE_MOI}}')) {
            $data['content'] = str_ireplace('{{FILE_MOI}}', '<a href="'. $linkDowloadNewFile
                .'" rel="noopener noreferrer" target="_blank"><strong>' . $newFile . '</strong></a>', $data['content']);
        }

        if (str_contains( $data['content'], '{{THOI_HAN_NHAN_KET_QUA}}')) {
            $data['content'] = str_ireplace('{{THOI_HAN_NHAN_KET_QUA}}', date('d/m/Y', $deadline), $data['content']);
        }
        if (str_contains( $data['content'], '{{YEU_CAU_CHI_TIET}}')) {
            $data['content'] = str_ireplace('{{YEU_CAU_CHI_TIET}}', $note, $data['content']);
        }
        if (str_contains( $data['content'], '{{LOAI}}')) {
            $data['content'] = str_ireplace('{{LOAI}}', $type == 0 ? 'Dịch' : 'Review' , $data['content']);
        }

        if (str_contains( $data['subject'], '{{TEN_KHACH_HANG}}')) {
            $data['subject'] = str_ireplace('{{TEN_KHACH_HANG}}', $user->name, $data['subject']);
        }

        if (str_contains( $data['subject'], '{{NGAY}}')) {
            $data['subject'] = str_ireplace('{{NGAY}}', Carbon::now()->format('d/m/Y'), $data['subject']);
        }

        return $data;
    }

    public function downloadFile($id)
    {
        try {
            $document = Document::find($id);
            if ($document) {
                if($document->type == Document::TYPE['REQUEST']) {
                    $contents = collect(Storage::disk('user_google')->listContents('/', false));
                } else {
                    $contents = collect(Storage::disk('admin_google')->listContents('/', false));
                }

                $file = $contents
                    ->where('type', '=', 'file')
                    ->where('filename', '=', pathinfo($document->name, PATHINFO_FILENAME))
                    ->where('extension', '=', pathinfo($document->name, PATHINFO_EXTENSION))
                    ->first();
                if($document->type == Document::TYPE['REQUEST']) {
                    $response = Storage::disk('user_google')->download($file['path'], $document->name);
                } else {
                    $response = Storage::disk('admin_google')->download($file['path'], $document->name);
                }

                $response->send();
            }
        } catch (Exception $e) {
            Log::error('Error download file', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
            ]);
            return $this->responseError();
        }
    }

    private function sendMailUpdateFileResult($user, $order, $file, $fileResult, $emailTemp, $linkDowloadFile,
                                              $linkDowloadFileResult)
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

        if (str_contains( $data['content'], '{{FILE}}')) {
            $data['content'] = str_ireplace('{{FILE}}', '<a href="'. $linkDowloadFile
                .'" rel="noopener noreferrer" target="_blank"><strong>' . $file . '</strong></a>',
                $data['content']);
        }

        if (str_contains( $data['content'], '{{FILE_KET_QUA}}')) {
            $data['content'] = str_ireplace('{{FILE_KET_QUA}}', '<a href="'. $linkDowloadFileResult
                .'" rel="noopener noreferrer" target="_blank"><strong>' . $fileResult . '</strong></a>',
                $data['content']);
        }

        if (str_contains( $data['content'], '{{LOAI}}')) {
            $data['content'] = str_ireplace('{{LOAI}}', $data['type'], $data['content']);
        }

        if (str_contains( $data['content'], '{{MA_DON_HANG}}')) {
            $data['content'] = str_ireplace('{{MA_DON_HANG}}', $order->code, $data['content']);
        }
        if (str_contains( $data['content'], '{{YEU_CAU_CHI_TIET}}')) {
            $data['content'] = str_ireplace('{{YEU_CAU_CHI_TIET}}', $order->note, $data['content']);
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

        if (str_contains( $data['subject'], '{{MA_DON_HANG}}')) {
            $data['subject'] = str_ireplace('{{MA_DON_HANG}}', $order->code, $data['subject']);
        }

        if (str_contains( $data['subject'], '{{YEU_CAU_CHI_TIET}}')) {
            $data['subject'] = str_ireplace('{{YEU_CAU_CHI_TIET}}', $order->note, $data['subject']);
        }

        return $data;
    }
}
