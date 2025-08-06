<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\Auth\GoogleController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BannerPositionController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\DiscountProductController;
use App\Http\Controllers\Api\EditorController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\PostCategoryController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\PromotionProductController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\VoucherController;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Promotion;

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
Route::post('/register', [AuthController::class, 'register']);


Route::post('/ai/generate-description', [AiController::class, 'generateDescription']);

//Home
Route::get("/home/getApiHomePage", [HomeController::class, 'homePageAPI']);
Route::get('/home/getApiHeaderFooter', [HomeController::class, 'headerFooterApi']);
Route::get("/home/cartInfoApi", [HomeController::class, 'cartInfoApi']);

//Cart
Route::post('/cart/add-product-to-cart', [CartController::class, 'addProductToCart']);

// Banner
Route::get('/banners/get-banner-homepage', [BannerController::class, 'getBannerHomePage']);
Route::get('/banners/get-banner-top', [BannerController::class, 'getBannerTop']);
Route::get('/banners/get-banner-product-latest', [BannerController::class, 'getBannerProductLatest']);
Route::get('/banners/get-banner-product-outstanding', [BannerController::class, 'getBannerProductOutstanding']);

// Product
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/search-multiple-products', [ProductController::class, 'searchMultipleProducts']);
Route::get('/products/latest', [ProductController::class, 'getLatest']);
Route::get('/products/trending', [ProductController::class, 'getProductTrending']);
Route::get('/products/segment', [ProductController::class, 'getProductSameSegment']);
Route::get('/products/all', [ProductController::class, 'getAllProductsNoPagination']);
Route::get('/products/getAllProductsByIds', [ProductController::class, 'getAllProductsByIds']);
Route::get('/products/getById/{id}', [ProductController::class, 'getById']);

// Promotions
Route::get('/promotions/all', [PromotionController::class, 'getAllPromotionsNoPagination']);
Route::get('/promotions/available', [PromotionController::class, 'getPromotionAvailable']);
Route::post('/promotions/sync-products', [PromotionController::class, 'syncProductsAndCategories']);

// Promotions products
Route::get("/promotions-products/get-promotions-and-products", [PromotionProductController::class, 'getPromotionsAndProducts']);
Route::get("/promotions-products/get-promotions-and-products-show-on-frontend", [PromotionController::class, 'getPromotionsWithProductsForFrontend']);
//Post
Route::get('/posts/search', [PostController::class, 'search']);
Route::get('/posts/search-multiple-posts', [PostController::class, 'searchMultiplePosts']);
Route::get('/posts/get_post_header_client' ,[PostController::class, 'getPostsHeader']);

//Post categories
Route::get('/post-categories/get-list-categories', [PostCategoryController::class, 'getListCategories']);
Route::get('/post-categories/all', [PostCategoryController::class, 'getAllPostCategoriesNoPagination']);

//Voucher
Route::get('/vouchers/getListApproved', [VoucherController::class, 'getListApproved']);

//Category
Route::get('/categories/getListApproved', [CategoryController::class, 'getListApproved']);
Route::get('/categories/getListOutstanding', [CategoryController::class, 'getListOutstanding']);
Route::get('/categories/getCategoryParentAndChildHeader', [CategoryController::class, 'getCategoryParentAndChildHeader']);
Route::get('/categories/available', [CategoryController::class, 'getAvailableCategories']);

// Policies
Route::get('/policies/getAll', [PolicyController::class, 'getAllPolicies']);

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

    // Banner positions
    Route::get('/banner-positions/banner-positions-select', [BannerPositionController::class, 'selectPositions']);

    //Promotions products
    Route::post('/promotions/promotions-products', [PromotionController::class, 'syncProducts']);
    Route::get('/promotions/{id}/products', [PromotionController::class, 'getProducts']);



    Route::apiResource('brands', BrandController::class);
    Route::apiResource('ads', AdvertisementController::class);
    Route::apiResource('vouchers', VoucherController::class);
    Route::apiResource('videos', VideoController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('product-images', ProductImageController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('discounts', DiscountController::class);
    Route::apiResource('discount-products', DiscountProductController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-categories', PostCategoryController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('promotions', PromotionController::class);
    Route::apiResource('promotions-products', PromotionProductController::class);
    Route::apiResource('banners', BannerController::class);
    Route::apiResource('policies', PolicyController::class);
    Route::apiResource('banner-positions', BannerPositionController::class);


    Route::post('/editor-upload', [EditorController::class, 'upload']);
});
