<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        $promotions = $query->latest()->paginate(10);

        return PromotionResource::collection($promotions);
    }


    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        return new PromotionResource($promotion);
    }

    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return new PromotionResource($promotion);
    }

    public function update(UpdatePromotionRequest $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update($request->validated());

        return new PromotionResource($promotion);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(['message' => 'Xóa thành công'], 200);
    }
}
