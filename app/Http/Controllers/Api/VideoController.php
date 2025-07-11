<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Resources\VideoResource;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('src', 'like', '%' . $search . '%');
        }

        $videos = $query->latest()->paginate(10);
        return VideoResource::collection($videos);
    }

    public function store(StoreVideoRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/videos', 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $video = Video::create($validated);

        return new VideoResource($video);
    }

    public function show($id)
    {
        $video = Video::findOrFail($id);
        return new VideoResource($video);
    }

    public function update(UpdateVideoRequest $request, $id)
    {
        $video = Video::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($video->image && str_starts_with($video->image, asset('storage'))) {
                $oldPath = str_replace(asset('storage') . '/', '', $video->image);
                Storage::disk('public')->delete($oldPath);
            }

            $imagePath = $request->file('image')->store('uploads/videos', 'public');
            $validated['image'] = asset('storage/' . $imagePath);
        }

        $video->update($validated);

        return new VideoResource($video);
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}
