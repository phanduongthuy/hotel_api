<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRule\StoreServiceRuleRequest;
use App\Models\ServiceRule;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class ServiceRuleController extends Controller
{
    use ResponseTrait;

    public function index() {
        $rule = ServiceRule::query()->first();

        return $this->responseSuccess($rule);
    }

    public function store(StoreServiceRuleRequest $request) {
        try {
            $serviceRule = ServiceRule::query()->first();
            if ($serviceRule) {
                $serviceRule->content = $request->input('content');
                $serviceRule->save();
            } else {
                $serviceRule = new ServiceRule();
                $serviceRule->content = $request->input('content');
                $serviceRule->save();
            }

            return $this->responseSuccess($serviceRule);
        } catch (Exception $e) {
            Log::error('Error store service rule', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }
}
