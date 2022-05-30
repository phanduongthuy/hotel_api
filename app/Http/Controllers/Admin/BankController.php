<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Models\BankAccount;
use App\Http\Requests\Admin\Bank\StoreBankRequest;
use App\Http\Requests\Admin\Bank\UpdateBankRequest;
use Exception;

class BankController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = BankAccount::query();
            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where('account_holder', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('bank_name', 'LIKE', '%' . $request->input('q') . '%');
            }
            $banks = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

            return $this->responseSuccess($banks);
        } catch (Exception $e) {
            Log::error('Error get list employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function getBankAccount()
    {
        try {
            $accountBank = BankAccount::first();
            if (!$accountBank) {
                return $this->responseError('Tài khoản ngân hàng không tồn tại.', [], 400);
            }
            return $this->responseSuccess($accountBank);
        } catch (Exception $e) {
            Log::error('Error get account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function store(StoreBankRequest $request)
    {
        try {
            if ($this->isExistAccountNumber($request->input('account_number'))) {
                return $this->responseError('Số tài khoản đã tồn tại.', [], 400);
            }

            $accountBank = new BankAccount();
            $accountBank->account_holder = $request->input('name');
            $accountBank->account_number = $request->input('account_number');
            $accountBank->bank_name = $request->input('bank_name');
            $accountBank->phone = $request->input('phone');

            $accountBank->save();
            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error store account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function update(UpdateBankRequest $request, $id)
    {
        try {
            $accountBank = BankAccount::find($id);
            if ($accountBank) {
                $count = BankAccount::where('account_number', $request->input('account_number'))
                    ->where('_id', '<>', $id)
                    ->count();
                if ($count > 0) {
                    return $this->responseError('Số tài khoản đã tồn tại.', [], 400);
                }

                $accountBank->account_holder = $request->input('name');
                $accountBank->account_number = $request->input('account_number');
                $accountBank->bank_name = $request->input('bank_name');
                $accountBank->phone = $request->input('phone');

                $accountBank->save();
            } else {
                return $this->responseError('Không có tài khoản ngân hàng này trong hệ thống!', [], 404);
            }
            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    public function handleCreateAndUpdateBank (StoreBankRequest $request)
    {
        try {
            $accountBank = BankAccount::first();
            if (!$accountBank) {
                $accountBank = new BankAccount();
            }

            $accountBank->account_holder = $request->input('name');
            $accountBank->account_number = $request->input('account_number');
            $accountBank->bank_name = $request->input('bank_name');
            $accountBank->note = $request->input('note');

            $accountBank->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        try {
            $accountBank = BankAccount::find($id);
            if ($accountBank) {
                $accountBank->delete();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Account bank not found', [], 404);
            }
        } catch (Exception $e) {
            Log::error('Error delete employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    private function isExistAccountNumber($accountNumber)
    {
        $count = BankAccount::where('account_number', $accountNumber)->count();
        return $count > 0;
    }
}
