<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Resources\BannerResource;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($request->has('position_id')) {
            $positionId = $request->input('position_id');
            if (!empty($positionId)) {
                $query->where('position_id', $positionId);
            }
        }

        $banners = $query->latest()->paginate(10);
        return BannerResource::collection($banners);
    }

    public function getBannerHomePage()
    {
        $banners = Banner::whereHas('position', function ($query) {
            $query->where('name', 'Trang chủ');
        })
            ->where('approved', 1)
            ->latest()
            ->get();

        return response()->json($banners);
    }

    public function getBannerTop()
    {
        $banners = Banner::whereHas('position', function ($query) {
            $query->where('name', 'Đầu trang');
        })
            ->where('approved', 1)
            ->latest()
            ->get();

        return response()->json($banners);
    }

    public function getBannerProductOutstanding()
    {
        $banners = Banner::whereHas('position', function ($query) {
            $query->where('name', 'Sản phẩm nổi bật');
        })
            ->where('approved', 1)
            ->latest()
            ->get();

        return response()->json($banners);
    }
    
    public function getBannerProductLatest()
    {
        $banners = Banner::whereHas('position', function ($query) {
            $query->where('name', 'Sản phẩm mới');
        })
            ->where('approved', 1)
            ->latest()
            ->get();

        return response()->json($banners);
    }


    public function getListApproved()
    {
        $banners = Banner::where('approved', 1)->get();
        return response()->json($banners);
    }

    public function selectBannersNotDeleted()
    {
        $banners = Banner::whereNull('deleted_at')->get()->map(function ($banner) {
            return [
                'value' => $banner->id,
                'label' => $banner->title,
            ];
        });

        return response()->json($banners);
    }

    public function store(StoreBannerRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image'] =  asset('storage/' . $path);
        }
        $banner = Banner::create($validated);
        return new BannerResource($banner);
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return new BannerResource($banner);
    }

    public function update(UpdateBannerRequest $request, $id)
    {
        $validated = $request->validated();
        $banner = Banner::findOrFail($id);
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu tồn tại
            if ($banner->image && File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }

            $path = $request->file('image')->store('banners', 'public');
            $validated['image'] =  asset('storage/' . $path);
        }
        $banner->update($validated);
        return new BannerResource($banner);
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        return response()->json(['message' => 'Đã xóa thành công'], 204);
    }
}
