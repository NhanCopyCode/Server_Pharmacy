<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePolicyRequest;
use App\Http\Requests\UpdatePolicyRequest;
use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PolicyController extends Controller
{
    public function index(Request $request)
    {
        $query = Policy::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $policies = $query->latest()->paginate(10);

        return PolicyResource::collection($policies);
    }

    public function store(StorePolicyRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('policies', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $policy = Policy::create($data);

        return new PolicyResource($policy);
    }

    public function show($id)
    {
        $policy = Policy::findOrFail($id);

        return new PolicyResource($policy);
    }

    public function update(UpdatePolicyRequest $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($policy->image) {
                $oldPath = str_replace('storage/', '', $policy->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('policies', 'public');
            $data['image'] = asset('storage/' . $path);
        }

        $policy->update($data);

        return new PolicyResource($policy);
    }

    public function destroy($id)
    {
        $policy = Policy::findOrFail($id);
        $policy->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }

    public function getAllPolicies()
    {
        $policies = Policy::all();

        return PolicyResource::collection($policies);
    }
}
