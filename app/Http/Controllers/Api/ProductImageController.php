<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function updateImagesForProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required|exists:products,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'existingImages' => 'array',
        ], [
            'productId.required' => 'ID sản phẩm là bắt buộc.',
            'productId.exists' => 'Sản phẩm không tồn tại.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productId = $request->input('productId');
        $existingImages = $request->input('existingImages', []);

        $currentImages = ProductImage::where('productId', $productId)->get();

        foreach ($currentImages as $image) {
            if (!in_array($image->image, $existingImages)) {
                $image->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');

                ProductImage::create([
                    'productId' => $productId,
                    'image' => asset('storage/' . $path),
                ]);
            }
        }

        return response()->json(['message' => 'Cập nhật ảnh thành công']);
    }


    public function deleteAllProductImagesByProductId(Request $request)
    {
        $request->validate([
            'productId' => 'required|exists:products,id',
        ], [
            'productId.required' => 'Thiếu productId.',
            'productId.exists' => 'Sản phẩm không tồn tại.',
        ]);

        $productId = $request->input('productId');

        $images = ProductImage::where('productId', $productId)->get();

        foreach ($images as $image) {
            if ($image->image) {
                // Remove from storage
                $filePath = str_replace(asset('storage') . '/', '', $image->image);
                \Storage::disk('public')->delete($filePath);
            }

            // Soft delete if using SoftDeletes
            $image->delete();
        }

        return response()->json([
            'message' => 'Tất cả hình ảnh của sản phẩm đã được xóa.',
            'deleted' => $images->count()
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required|exists:products,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // limit to 2MB
        ], [
            'productId.required' => 'ID sản phẩm là bắt buộc.',
            'productId.exists' => 'Sản phẩm không tồn tại.',
            'images.required' => 'Vui lòng chọn ít nhất một hình ảnh.',
            'images.array' => 'Trường hình ảnh phải là một mảng.',
            'images.*.image' => 'Tập tin phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'images.*.max' => 'Kích thước mỗi hình ảnh không vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $savedImages = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('product_images', 'public');

            $image = ProductImage::create([
                'productId' => $request->input('productId'),
                'image' => asset('storage/' . $path),
            ]);

            $savedImages[] = $image;
        }

        return response()->json([
            'message' => 'Tải lên thành công',
            'data' => $savedImages,
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductImage  $productImage
     * @return \Illuminate\Http\Response
     */
    public function show(ProductImage $productImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductImage  $productImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductImage $productImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductImage  $productImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductImage $productImage)
    {
        //
    }

    public function getImagesByProductId(Request $request)
    {
        $productId = $request->input('productId');

        if (!$productId) {
            return response()->json([
                'message' => 'Missing productId'
            ], 400);
        }

        $images = ProductImage::where('productId', $productId)->get();

        return response()->json($images);
    }
}
