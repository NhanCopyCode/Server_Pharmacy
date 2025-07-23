<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\SyncPromotionProductsRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionProductResource;
use App\Http\Resources\PromotionResource;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::with('products', 'categories');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $promotions_products = $query->latest()->paginate(10);

        return PromotionProductResource::collection($promotions_products);
    }


    public function store(StorePromotionRequest $request)
    {
        $promotion = Promotion::create($request->validated());

        $productIdsFromRequest = $request->input('product_ids', []);
        $categoryIds = $request->input('category_ids', []);

        $productIdsFromCategories = [];
        if (!empty($categoryIds)) {
            $productIdsFromCategories = \App\Models\Product::whereIn('categoryId', $categoryIds)
                ->pluck('id')
                ->toArray();
        }

        $allProductIds = collect($productIdsFromRequest)
            ->merge($productIdsFromCategories)
            ->unique()
            ->values()
            ->toArray();

        $promotion->products()->sync($allProductIds);
        $promotion->categories()->sync($categoryIds);

        return new PromotionProductResource(
            $promotion->load(['products', 'categories'])
        );
    }


    public function show($id)
    {
        $promotion = Promotion::with(['products', 'categories'])->findOrFail($id);
        return new PromotionProductResource($promotion);
    }

    public function update(SyncPromotionProductsRequest $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $promotion->update($request->validated());

        $productIdsFromRequest = $request->input('product_ids', []);
        $categoryIds = $request->input('category_ids', []);

        $productIdsFromCategories = [];
        if (!empty($categoryIds)) {
            $productIdsFromCategories = \App\Models\Product::whereIn('categoryId', $categoryIds)
                ->pluck('id')
                ->toArray();
        }

        $allProductIds = collect($productIdsFromRequest)
            ->merge($productIdsFromCategories)
            ->unique()
            ->values()
            ->toArray();

        $promotion->products()->sync($allProductIds);
        $promotion->categories()->sync($categoryIds);

        return new PromotionProductResource(
            $promotion->load(['products', 'categories'])
        );
    }


    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->products()->detach();
        $promotion->categories()->detach();
        $promotion->delete();

        return response()->json(['message' => 'Xóa khuyến mãi thành công.']);
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

    public function getPromotionAvailable()
    {
        $promotions = Promotion::where('approved', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return PromotionResource::collection($promotions);
    }

    public function getPromotionsAndProducts()
    {
        $products = Product::whereHas('promotions', function ($query) {
            $query->where('approved', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        })
            ->with(['promotions' => function ($q) {
                $q->where('approved', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->with('categories');
            }])
            ->take(5)
            ->get();

        return response()->json($products);
    }
}
