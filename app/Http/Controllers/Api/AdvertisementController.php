<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Http\Resources\AdvertisementResource;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $query = Advertisement::query();

        // Optional: search by title
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        $ads = $query->paginate(10);
        return AdvertisementResource::collection($ads);
    }

    public function listSelectAds()
    {
        $ads = Advertisement::get()->map(function ($ad) {
            return [
                'value' => $ad->id,
                'label' => $ad->title,
            ];
        });

        return response()->json($ads);
    }

    public function store(StoreAdvertisementRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ads', 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $ad = Advertisement::create($validated);
        return new AdvertisementResource($ad);
    }

    public function show($id)
    {
        $ad = Advertisement::findOrFail($id);
        return new AdvertisementResource($ad);
    }

    public function update(UpdateAdvertisementRequest $request, $id)
    {
        $ad = Advertisement::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ads', 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $ad->update($validated);
        return new AdvertisementResource($ad);
    }

    public function destroy($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->delete();
        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}
