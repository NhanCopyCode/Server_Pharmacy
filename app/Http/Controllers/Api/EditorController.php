<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('editor-images', 'public');

            return response()->json([
                'errorMessage' => null,
                'result' => [
                    'url' => asset("storage/{$path}"),
                ],
            ]);
        }

        return response()->json([
            'errorMessage' => 'No file uploaded.',
        ], 400);
    }
}
