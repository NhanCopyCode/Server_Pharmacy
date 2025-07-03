<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        $posts = $query->with('user')->paginate(10);
        return PostResource::collection($posts);
    }

    public function getListPosts()
    {
        $posts = Post::select('id', 'title')->get()->map(function ($post) {
            return [
                'value' => $post->id,
                'label' => $post->title,
            ];
        });

        return response()->json($posts);
    }

    public function selectPostsNotDeleted()
    {
        $posts = Post::whereNull('deleted_at')->get()->map(function ($post) {
            return [
                'value' => $post->id,
                'label' => $post->title,
            ];
        });

        return response()->json($posts);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $post = Post::create($validated);
        return new PostResource($post);
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $validated = $request->validated();
        $post->update($validated);
        return new PostResource($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Đã xóa thành công'], 204);
    }
}
