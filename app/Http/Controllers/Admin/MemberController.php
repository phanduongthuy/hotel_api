<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Http\Requests\Admin\Member\StoreMemberRequest;
use App\Http\Requests\Admin\Member\UpdateMemberRequest;
use App\Models\Member;


class MemberController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Member::query();
            if ($request->has('q') && strlen($request->input('q')) > 0) {
                $query->where('name', 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('email', 'LIKE', '%' . $request->input('q') . '%');
            }
            $employees = $query->orderBy('created_at', 'DESC')->paginate(config('constants.per_page'));

            return $this->responseSuccess($employees);
        } catch (Exception $e) {
            Log::error('Error get list employee', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function getAllMembers()
    {
        try {
            $employees = Member::orderBy('created_at', 'DESC')->get();
            return $this->responseSuccess($employees);
        } catch (Exception $e) {
            Log::error('Error get all list member', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function store(StoreMemberRequest $request)
    {
        try {
            $member = new Member();
            $member->name = $request->input('name');
            $member->email = $request->input('email');
            $member->phone = $request->input('phone');
            $member->address = $request->input('address');
            $member->save();

            if ($request->hasFile('image')) {
                $path = Storage::disk('public')->putFile('images/members/' . $member->_id, $request->file('image'));
                $member->image = $path;
                $member->save();
            }

            return $this->responseSuccess();

        } catch (Exception $e) {
            Log::error('Error store member', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
                'data' => $request->all()
            ]);
            return $this->responseError();
        }
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        try {
            $member = Member::find($id);
            if ($member) {
                $member->name = $request->input('name') ;
                $member->email = $request->input('email')? $request->input('email') : '';
                $member->phone = $request->input('phone')? $request->input('phone') : '';
                $member->address = $request->input('address')? $request->input('address') : '';

                if ($request->hasFile('image')) {
                    Storage::disk('public')->deleteDirectory('images/members/' . $member->_id);
                    $path = Storage::disk('public')->putFile('images/members/' . $member->id, $request->file('image'));
                    $member->image = $path;
                }

                $member->save();
            } else {
                return $this->responseError('Không có thành viên này trong hệ thống!', [], 404);
            }
            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update member', [
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
            $member = Member::find($id);
            if ($member) {
                $member->delete();
                Storage::disk('public')->deleteDirectory('images/members/' . $member->_id);
                return $this->responseSuccess();
            } else {
                return $this->responseError('Member not found', [], 404);
            }
        } catch (Exception $e) {
            Log::error('Error delete member', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }
}
