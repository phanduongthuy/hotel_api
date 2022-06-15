<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->has('q') && strlen($request->input('q')) > 0) {
            $query->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        if ($request->has('is_highlight') && strlen($request->input('is_highlight')) > 0) {
            $query->where('is_highlight', (boolean)((int)$request->input('is_highlight')));
        }
        if ($request->has('category_id') && strlen($request->input('category_id')) > 0) {
            $query->where('category_id', $request->input('category_id'));
        }

        $rooms = $query->orderBy('is_highlight', 'DESC')
            ->latest()
            ->with('category')
            ->paginate(env('PER_PAGE'));
        return $this->responseSuccess($rooms);
    }


    public function store(Request $request)
    {
        try {

            $room = new Room();
            $room->name = $request->input('name');
            $room->category_id = $request->input('category_id');
            $room->priceOneHour = $request->input('priceOneHour');
            $room->priceOneNight = $request->input('priceOneNight');
            $room->description = $request->input('description');
            $room->is_highlight = (boolean)$request->input('is_highlight');
            $room->is_active = true;

            $room->save();

            return $this->responseSuccess();

        } catch (\Exception $e) {
            Log::error('Error store room', [
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
            $room = Room::find($id);
            if ($room) {
                $room->name = $request->input('name');
                $room->category_id = $request->input('category_id');
                $room->priceOneHour = $request->input('priceOneHour');
                $room->priceOneNight = $request->input('priceOneNight');
                $room->description = $request->input('description');
                $room->is_highlight = (boolean)$request->input('is_highlight');

                $room->save();


            } else {
                return $this->responseError('Không có phòng này trong hệ thống!', [], 404);
            }
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update room', [
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
            $category = Room::find($id);
            if ($category) {
                $category->delete();
                return $this->responseSuccess();
            } else {
                return $this->responseError('Room not found', [], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error delete room', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
                'line' => __LINE__
            ]);

            return $this->responseError();
        }
    }

    public function activeRoom(Request $request, $id)
    {
        try {
            $product = Room::findOrFail($id);
            if ($product) {
                $product->is_active = (boolean)$request->input('is_active');
                $product->save();
            } else {
                return $this->responseError('Không có phòng này trong hệ thống!', [], 404);
            }

            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::error('Error update room', [
                'method' => __METHOD__,
                'message' => $e->getMessage()
            ]);

            return $this->responseError();
        }
    }

    public function getAllRoomActive()
    {
        $rooms = Room::where('is_active', true)->get();

        return $this->responseSuccess($rooms);
    }

}
