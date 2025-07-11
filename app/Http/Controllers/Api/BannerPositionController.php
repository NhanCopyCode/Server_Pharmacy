<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerPositionRequest;
use App\Http\Requests\UpdateBannerPositionRequest;
use Illuminate\Http\Request;
use App\Models\BannerPosition;
use App\Http\Resources\BannerPositionResource;

class BannerPositionController extends Controller
{
    public function index(Request $request)
    {
        $query = BannerPosition::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $positions = $query->latest()->paginate(10);
        return BannerPositionResource::collection($positions);
    }

    public function selectPositions()
    {
        $positions = BannerPosition::get()->map(function ($position) {
            return [
                'value' => $position->id,
                'label' => $position->name,
            ];
        });

        return response()->json($positions);
    }

    public function store(StoreBannerPositionRequest $request)
    {
        $validated = $request->validated();
        $position = BannerPosition::create($validated);
        return new BannerPositionResource($position);
    }

    public function show($id)
    {
        $position = BannerPosition::findOrFail($id);
        return new BannerPositionResource($position);
    }

    public function update(UpdateBannerPositionRequest $request, $id)
    {
        $validated = $request->validated();
        $position = BannerPosition::findOrFail($id);
        $position->update($validated);
        return new BannerPositionResource($position);
    }

    public function destroy($id)
    {
        $position = BannerPosition::findOrFail($id);
        $position->delete();
        return response()->json(['message' => 'Đã xóa thành công'], 204);
    }
}
