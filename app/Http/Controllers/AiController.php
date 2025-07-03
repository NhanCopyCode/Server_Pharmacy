<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function generateDescription(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $name = $request->input('name');
        $keyword = $request->input('keyword', 'sản phẩm'); 

        $extraDescriptions = collect($request->except(['name', 'keyword']))
            ->map(function ($value, $key) {
                return ucfirst($key) . ": " . $value;
            })
            ->values()
            ->implode(". ");

        $prompt = "Viết một đoạn mô tả bằng tiếng Việt, chuyên nghiệp và chuẩn SEO cho một \"{$keyword}\" tên là \"{$name}\". Đừng có tạo những câu không cần thiết, vì tôi đang tạo 1 nút copy cho mô tả này. Hãy tập trung vào việc mô tả công dụng, thành phần, cách sử dụng và các lưu ý quan trọng liên quan đến \"{$name}\". Đoạn mô tả nên dài từ 100 đến 300 từ, súc tích và dễ hiểu.";

        if (!empty($extraDescriptions)) {
            $prompt .= " Dưới đây là một số thông tin thêm: {$extraDescriptions}.";
        }
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . env('GEMINI_API_KEY', 'AIzaSyCSjum3RFJKPCWZ4zIGodU5c67wE4NJucs');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Yêu cầu AI thất bại',
                'gemini_api' => env('GEMINI_API_KEY'),
                'error' => $response->json()
            ], 500);
        }

        $content = $response->json('candidates.0.content.parts.0.text');

        return response()->json([
            'description' => trim($content),
        ]);
    }
}
