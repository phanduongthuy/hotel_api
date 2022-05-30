<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PriceList\StorePriceListRequest;
use App\Models\PriceList;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class PriceListController extends Controller
{
    use ResponseTrait;
    public function index(){
        $priceList = PriceList::first();

        return $this->responseSuccess($priceList);
    }
    public function store(StorePriceListRequest $request){
        try {
            $priceList = PriceList::first();
            if (!$priceList) {
                $priceList = new PriceList();
            }
            $priceList->title = $request->input('title');
            $priceList->description = $request->input('description');
            if ($request->hasFile('image')) {
                $path = Storage::disk('public')->putFile('images/priceList/', $request->file('image'));
                $priceList->image = $path;
            }
            $priceList->save();
        } catch (Exception $e) {
            Log::error('Error store price list', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__,
                'data' => $request->all()
            ]);
            return $this->responseError();
        }

    }
}
