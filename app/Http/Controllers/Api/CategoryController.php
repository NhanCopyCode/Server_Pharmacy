<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        } elseif ($request->filled('parentId')) {
            $parentId = $request->input('parentId');
            $query->where('id', $parentId);
        } else {
            $query->where('parentId', 0);
        }

        $parents = $query->with('children')->latest()->paginate(10);

        $listParents = Category::where('parentId', 0)->get()->map(function ($parent) {
            return [
                'value' => $parent->id,
                'label' => $parent->name,

            ];
        });

        $nested = $parents->map(function ($parent) {
            return [
                'id' => $parent->id,
                'name' => $parent->name,
                'image' => $parent->image,
                'outstanding' => $parent->outstanding,
                'approved' => $parent->approved,
                'children' => $parent->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'image' => $child->image,
                        'outstanding' => $child->outstanding,
                        'parentName' => $child->parent ? $child->parent->name : null,
                        'approved' => $child->approved,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $nested,
            'listParents' => $listParents,
            'links' => [
                'first' => $parents->url(1),
                'last' => $parents->url($parents->lastPage()),
                'prev' => $parents->previousPageUrl(),
                'next' => $parents->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $parents->currentPage(),
                'from' => $parents->firstItem(),
                'last_page' => $parents->lastPage(),
                'links' => $parents->linkCollection(),
                'path' => $request->url(),
                'per_page' => $parents->perPage(),
                'to' => $parents->lastItem(),
                'total' => $parents->total(),
            ]
        ]);
    }

    public function getListApproved()
    {
        $categories = Category::where('approved', 1)->get();

        return response()->json($categories);
    }

    public function getListOutstanding()
    {
        $categories = Category::where('outstanding', 1)
            ->where('approved', 1)
            ->get();

        return response()->json($categories);
    }

    public function getCategoryParentAndChild()
    {
        $categories = Category::where('parentId', 0)
            ->where('approved', 1)
            ->with(['children' => function ($query) {
                $query->where('approved', 1);
            }])
            ->get();

        return response()->json($categories);
    }

    public function getParents(Request $request)
    {
        $parents = Category::where('parentId', 0)->get();

        $parentsObject = $parents->map(function ($parent) {
            return [
                'value' => $parent->id,
                'label' => $parent->name,
            ];
        });

        return response()->json($parentsObject);
    }

    public function getChildNotDeleted(Request $request)
    {
        $parents = Category::where('parentId', '!=',  0)
            ->whereNull('deleted_at')
            ->get();

        $parentsObject = $parents->map(function ($parent) {
            return [
                'value' => $parent->id,
                'label' => $parent->name,
            ];
        });

        return response()->json($parentsObject);
    }

    public function getChild(Request $request)
    {
        $child = Category::where('parentId', '!=', 0)->get();

        $childObject  = $child->map(function ($childItem) {
            return [
                'value' => $childItem->id,
                'label' => $childItem->name
            ];
        });

        return response()->json($childObject);
    }


    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $category = Category::create($data);
        return new CategoryResource($category);
    }


    public function show($id)
    {
        $category = Category::findOrFail($id);
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $category->update($data);
        return new CategoryResource($category);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Deleted successfully'], 204);
    }
}
