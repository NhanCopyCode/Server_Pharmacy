<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PromotionResource;
use App\Models\Product;
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
        if ($request->filled('applies_to')) {
            $query->where('applies_to', $request->applies_to);
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

    public function syncProductsAndCategories(Request $request)
    {
        $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $promotion = Promotion::findOrFail($request->promotion_id);

        $productIdsFromRequest = $request->product_ids ?? [];
        $categoryIds = $request->category_ids ?? [];

        // Lấy tất cả product_id từ các category
        $productIdsFromCategories = [];
        if (!empty($categoryIds)) {
            $productIdsFromCategories = \App\Models\Product::whereIn('categoryId', $categoryIds)
                ->pluck('id')
                ->toArray();
        }

        // Gộp tất cả product_ids lại, tránh trùng lặp
        $allProductIds = collect($productIdsFromRequest)
            ->merge($productIdsFromCategories)
            ->unique()
            ->values()
            ->toArray();

        // Sync sản phẩm
        $promotion->products()->sync($allProductIds);

        // Sync danh mục
        if (!empty($categoryIds)) {
            $promotion->categories()->sync($categoryIds);
        }

        return response()->json([
            'message' => 'Cập nhật sản phẩm và danh mục thành công.',
            'promotion_id' => $promotion->id,
            'products' => $promotion->products()->get(['products.id', 'products.title']),
            'categories' => $promotion->categories()->get(['categories.id', 'categories.name']),
        ]);
    }



    public function getProducts($id)
    {
        $promotion = Promotion::with('products')->findOrFail($id);
        return response()->json($promotion->products);
    }

    public function getAllPromotionsNoPagination()
    {
        $promotions = Promotion::where('approved', 1)->get();

        return PromotionResource::collection($promotions);
    }

    public function getPromotionsWithProductsForFrontend()
    {
        $promotions = Promotion::where('approved', 1)
            ->where('show_on_frontend', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['products' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(10);
            }])
            ->orderBy('start_date', 'asc')
            ->take(10)
            ->get();

        return PromotionResource::collection($promotions);
    }
}
