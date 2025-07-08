<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['brand', 'category']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('brandId')) {
            $query->where('brandId', $request->input('brandId'));
        }

        if ($request->filled('categoryId')) {
            $query->where('categoryId', $request->input('categoryId'));
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }


    public function search(Request $request)
    {
        $input = $request->query('q');

        $query = Product::where('title', 'like', '%' . $input . '%')
            ->where('approved', 1);

        $productsCount = $query->count();
        $listProducts = $query->limit(4)->get();

        return response()->json([
            'listProducts' => $listProducts,
            'productsCount' => $productsCount
        ]);
    }

    public function searchMultipleProducts(Request $request)
    {
        $input = $request->query('q');

        $query = Product::where('title', 'like', '%' . $input . '%')
            ->where('approved', 1);

        $productsCount = $query->count();
        $listProducts = $query->limit(30)->get();

        return response()->json([
            'listProducts' => $listProducts,
            'productsCount' => $productsCount
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $$data['main_image'] = asset('storage/' . $path);
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = Product::with(['brand', 'category', 'images'])->findOrFail($id);
        return new ProductResource($product);
    }


    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                $oldPath = str_replace('storage/', '', $product->main_image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('main_image')->store('products', 'public');
            $data['main_image'] = asset('storage/' . $path);
        }

        $product->update($data);

        return new ProductResource($product);
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->main_image) {
            $oldPath = str_replace('storage/', '', $product->main_image);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }

    public function import(Request $request)
    {
        $data = $request->input('data');

        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                Product::create([
                    'title' => $row['title'] ?? 'Không tiêu đề',
                    'description' => $row['description'] ?? '',
                    'inventory' => $row['inventory'] ?? 0,
                    'categoryId' => $row['categoryId'] ?? null,
                    'price' => $row['price'] ?? 0,
                    'brandId' => $row['brandId'] ?? null,
                    'outstanding' => $row['outstanding'] ?? 0,
                    'approved' => $row['approved'] ?? 0,
                ]);
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $index + 1,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => "Đã import {$importedCount} sản phẩm thành công.",
            'errors' => $errors
        ]);
    }
}
