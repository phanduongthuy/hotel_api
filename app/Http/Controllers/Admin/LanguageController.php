<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Language\StoreLanguageRequest;
use App\Http\Requests\Admin\Language\UpdateLanguageRequest;
use App\Models\Language;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    use ResponseTrait;
    /**
     * Get all language.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Language::query();

            if ($request->has('q')) {
                $query->where('name', 'like', '%' . $request->input('q') . '%');
            }

            $query = $query->orderBy('name', 'ASC');

            $listLanguage = $query->paginate(config('constants.per_page'));

            return $this->responseSuccess($listLanguage);
        } catch (\Exception $e) {
            Log::error('Error get list language', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }

    public function store(StoreLanguageRequest $request)
    {
        try {
            $language = new Language();
            $language->name = $request->input('name');
            $language->description = $request->input('description');
            $language->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error store language', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }

    public function update(UpdateLanguageRequest $request, $id)
    {
        try {
            $language = Language::find($id);
            $language->name = $request->input('name');
            $language->description = $request->input('description');
            $language->save();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error update account bank', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }

    public function destroy($id)
    {
        try {
            $language = Language::find($id);
            $language->delete();

            return $this->responseSuccess();
        } catch (Exception $e) {
            Log::error('Error delete language', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);

            return $this->responseError();
        }
    }
}
