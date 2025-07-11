<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Resources\BrandResource;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query();

        // Optional: search by name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
            // ->orWhere('description', 'like', '%' . $search . '%');
        }

        $brands = $query->latest()->paginate(10);
        return BrandResource::collection($brands);
    }

    public function getListBrands()
    {

        $brands = Brand::get()->map(function ($brand) {
            return [
                'value' => $brand->id,
                'label' => $brand->name,

            ];
        });

        return response()->json($brands);
    }


    public function selectBrandsNotDeleted(Request $request)
    {
        $brands = Brand::whereNull('deleted_at')->get()->map(function ($brand) {
            return [
                'value' => $brand->id,
                'label' => $brand->name,

            ];
        });

        return response()->json($brands);
    }

    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = asset('storage/' . $logoPath);
        }

        $brand = Brand::create($validated);
        return new BrandResource($brand);
    }


    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validated();
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = asset('storage/' . $logoPath);
        }
        $brand->update($validated);
        return new BrandResource($brand);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}
