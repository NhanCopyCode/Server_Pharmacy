<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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

        $products = $query->latest()->paginate(10);

        return ProductResource::collection($products);
    }

    public function getProductSameSegment(Request $request)
    {
        $request->validate([
            'categoryId' => 'required|integer|exists:categories,id',
            'productId' => 'nullable|integer|exists:products,id',
        ]);

        $query = Product::query()
            ->where('categoryId', $request->categoryId)
            ->where('approved', 1)
            ->with(['images', 'brand', 'category']);

        if ($request->filled('productId')) {
            $query->where('id', '!=', $request->productId);
        }

        $products = $query->latest()->take(10)->get();

        return ProductResource::collection($products);
    }
    
    public function getLatest()
    {
        $products = Product::where('approved', 1)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return ProductResource::collection($products);
    }

    public function getProductTrending()
    {
        $products = Product::where('approved', 1)
            ->where('outstanding', 1)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

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
        $perPage = 30;

        $query = Product::where('title', 'like', '%' . $input . '%')
            ->where('approved', 1);

        $paginated = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'listProducts' => $paginated->items(),
            'productsCount' => $paginated->total(),
            'meta' => $paginated->toArray(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            $data['main_image'] = asset('storage/' . $path);
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
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $drawings = $sheet->getDrawingCollection();
        $imageMap = [];

        foreach ($drawings as $drawing) {
            $coords = $drawing->getCoordinates(); // ví dụ "H6"
            preg_match('/[A-Z]+(\d+)/', $coords, $matches);
            $rowIndex = (int)$matches[1];

            if ($drawing instanceof MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageData = ob_get_clean();
                $extension = 'jpg';
            } else {
                $imageData = file_get_contents($drawing->getPath());
                $extension = pathinfo($drawing->getPath(), PATHINFO_EXTENSION);
            }

            $filename = 'products/' . Str::uuid() . '.' . $extension;
            Storage::disk('public')->put($filename, $imageData);
            $imageMap[$rowIndex] = $filename;
        }

        $importedCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            try {
                Product::create([
                    'title' => $row[0] ?? 'Không tiêu đề',
                    'description' => $row[1] ?? '',
                    'inventory' => $row[2] ?? 0,
                    'categoryId' => $row[3] ?? null,
                    'price' => $row[4] ?? 0,
                    'brandId' => $row[5] ?? null,
                    'outstanding' => $row[6] ?? 0,
                    'approved' => $row[10] ?? 0,
                    'main_image' => isset($imageMap[$index + 1]) ? 'storage/' . $imageMap[$index + 1] : null,
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

    public function getAllProductsNoPagination() {
        $products = Product::where('approved', 1)->get();

        return ProductResource::collection($products);
    }
    
}
