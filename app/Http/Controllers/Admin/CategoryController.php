<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('q') && strlen($request->input('q')) > 0) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        if ($request->has('is_highlight') && strlen($request->input('is_highlight')) > 0) {
            $query->where('is_highlight', (boolean)((int)$request->input('is_highlight')));
        }

        $categories = $query->orderBy('is_highlight', 'DESC')
            ->latest()
            ->paginate(env('PER_PAGE'));
        return $this->responseSuccess($categories);
    }


    public function store(Request $request)
    {
        try {

            if ($this->isExistCategory($request->name)) {
                return $this->responseError('Danh mục này đã tồn tại.', [], 400);
            }
            $category = new Category();
            $category->name = $request->input('name');
            $category->description = $request->input('description');
            $category->is_highlight = (boolean)$request->input('is_highlight');
            $category->save();

            return $this->responseSuccess();

        } catch (\Exception $e) {
            Log::error('Error store category', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);
            return $this->responseError();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            if ($category) {
                $category->name = $request->input('name');
                $category->description = $request->input('description');
                $category->is_highlight = (boolean)$request->input('is_highlight');
                $category->save();
            } else {
                return $this->responseError('Không có danh mục này trong hệ thống!', [], 404);
            }
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update category', [
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
            $category = Category::find($id);
            if ($category) {
                $category->delete();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Category not found', [], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error delete category', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    private function isExistCategory($categoryName)
    {
        $count = Category::where('name', $categoryName)->count();
        return $count > 0;
    }

    public function getAllCategories()
    {
        $query = Category::query();
        $categories = $query->orderBy('is_highlight', 'DESC')->latest()->get();

        return $this->responseSuccess($categories);
    }
}
