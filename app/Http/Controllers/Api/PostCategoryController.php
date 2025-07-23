<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostCategoryRequest;
use App\Http\Requests\UpdatePostCategoryRequest;
use App\Models\PostCategory;
use App\Http\Resources\PostCategoryResource;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PostCategory::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        $categories = $query->latest()->paginate(10);
        return PostCategoryResource::collection($categories);
    }

    public function getListCategories()
    {
        $categories = PostCategory::select('id', 'title')->where('approved', 1)->get()->map(function ($category) {
            return [
                'value' => $category->id,
                'label' => $category->title,
            ];
        });

        return response()->json($categories);
    }

    public function selectCategoriesNotDeleted()
    {
        $categories = PostCategory::whereNull('deleted_at')->get()->map(function ($category) {
            return [
                'value' => $category->id,
                'label' => $category->title,
            ];
        });

        return response()->json($categories);
    }

    public function store(StorePostCategoryRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('post-categories', 'public');
            $validated['image'] = asset('storage/' . $path);
        }

        $category = PostCategory::create($validated);
        return new PostCategoryResource($category);
    }

    public function show($id)
    {
        $category = PostCategory::findOrFail($id);
        return new PostCategoryResource($category);
    }

    public function update(UpdatePostCategoryRequest $request, $id)
    {
        $category = PostCategory::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('post-categories', 'public');
            $validated['image'] = asset('storage/' . $path);
        }

        $category->update($validated);
        return new PostCategoryResource($category);
    }

    public function destroy($id)
    {
        $category = PostCategory::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Đã xóa thành công'], 204);
    }

    public function getAllPostCategoriesNoPagination() 
    {
        $post_categories = PostCategory::where('approved', 1)->get();

        return PostCategoryResource::collection($post_categories);
    }
}
