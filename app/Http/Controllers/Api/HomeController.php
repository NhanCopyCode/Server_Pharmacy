<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Policy;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Video;
use App\Models\Voucher;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function homePageAPI()
    {
        $banners_homepage  = Banner::getBannersHomepage();
        $categories_outstanding = CategoryResource::collection(Category::getListOutstanding());
        $vouchers = Voucher::getListApproved();
        // $categories_header = Category::getCategoryParentAndChild();
        $promotions_show_on_frontend = Promotion::getPromotionShowOnFrontend();
        $policies = Policy::where('approved', 1)->get();
        $videos = Video::where('approved', 1)->get();
        $posts_nutrition = Post::where('post_category_id', 1)->where('approved', 1)->take(5)->get();
        $post_beautiful_young = Post::where('post_category_id', 2)->where('approved', 1)->take(4)->get();
        $banners_outstanding = Banner::whereHas('position', function ($query) {
            $query->where('name', 'Sản phẩm nổi bật');
        })
            ->where('approved', 1)
            ->latest()
            ->get();
        $products_trending = Product::where('approved', 1)
            ->where('outstanding', 1)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Góc dinh dưỡng
        $posts_nutrition = Post::with(['category:id,title,approved,created_at,updated_at,deleted_at'])
            ->where('post_category_id', 1)
            ->where('approved', 1)
            ->select('id', 'title', 'approved', 'created_at', 'updated_at', 'deleted_at', 'post_category_id')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Góc trẻ đẹp
        $posts_beautiful_young = Post::with(['category:id,title,approved,created_at,updated_at,deleted_at'])
            ->where('post_category_id', 2)
            ->where('approved', 1)
            ->select('id', 'title', 'approved', 'created_at', 'updated_at', 'deleted_at', 'post_category_id')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();


        return response()->json([
            'banners_homepage' => $banners_homepage,
            'categories_outstanding' => $categories_outstanding,
            'vouchers' => $vouchers,
            // 'categories_header' => $categories_header,
            'promotions_show_on_frontend' => $promotions_show_on_frontend,
            'new_products' => ProductResource::collection(Product::getLatest()),
            'policies' => $policies,
            'videos' => $videos,
            'posts_nutrition' => $posts_nutrition,
            'banners_outstanding' => $banners_outstanding,
            'products_trending' => $products_trending,
            'posts_beautiful_young' => $posts_beautiful_young,
            'posts_nutrition' => $posts_nutrition
        ]);
    }

    public function headerFooterApi(Request $request)
    {
        $banners_top =  Banner::whereHas('position', function ($query) {
            $query->where('name', 'Đầu trang');
        })
            ->where('approved', 1)
            ->latest()
            ->get();

        $categories = Category::where('parentId', 0)
            ->where('approved', 1)
            ->whereHas('positions', function ($q) {
                $q->where('position', 'header');
            })
            ->with(['children' => function ($query) {
                $query->where('approved', 1)
                    ->whereHas('positions', function ($q) {
                        $q->where('position', 'header');
                    });
            }])
            ->get();
        $post_category = PostCategory::where('approved', 1)->get();
        $posts_header = Post::where('approved', 1)->orderBy('created_at', 'desc')->take(5)->get();
        return response()->json([
            'banners_top' => $banners_top,
            'categories' => $categories,
            'post_category' => $post_category,
            'posts_header' => $posts_header,
        ]);
    }

    public function cartInfoApi()
    {
        $vouchers = Voucher::getListApproved();

        return response()->json([
            'vouchers' => $vouchers,
        ]);
    }
}
