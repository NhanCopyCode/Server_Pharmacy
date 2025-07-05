<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\DiscountProductController;
use App\Http\Controllers\Api\EditorController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\VoucherController;
use App\Models\ProductImage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/me', [AuthController::class, 'me']);


Route::post('/ai/generate-description', [AiController::class, 'generateDescription']);


// API Resources
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    
    Route::get('/categories/parents', [CategoryController::class, 'getParents']);
    Route::get('/categories/child', [CategoryController::class, 'getChild']);
    Route::get('/categories/childNotDeleted', [CategoryController::class, 'getChildNotDeleted']);

    Route::get('/brands/selectBrands', [BrandController::class, 'getListBrands']);
    Route::get('/brands/selectBrandsNotDeleted', [BrandController::class, 'selectBrandsNotDeleted']);

    Route::post('/product-images/getImagesByProductId', [ProductImageController::class, 'getImagesByProductId']);
    Route::post('/product-images/deleteAllImagesByProductId', [ProductImageController::class, 'deleteAllProductImagesByProductId']);
    Route::post('/product-images/update-by-product-id', [ProductImageController::class, 'updateImagesForProduct']);

    // Excel upload
    Route::post('/products/import', [ProductController::class, 'import']);

    Route::apiResource('brands', BrandController::class);
    Route::apiResource('ads', AdvertisementController::class);
    Route::apiResource('videos', VideoController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('product-images', ProductImageController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('discounts', DiscountController::class);
    Route::apiResource('discount-products', DiscountProductController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('promotions', PromotionController::class);
    Route::apiResource('vouchers', VoucherController::class);
    
    Route::post('/editor-upload', [EditorController::class, 'upload']);
});
