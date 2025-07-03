<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'src' => 'sometimes|required|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'approved' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên video không được để trống.',
            'name.string' => 'Tên video phải là chuỗi.',
            'name.max' => 'Tên video không được vượt quá 255 ký tự.',

            'src.required' => 'Link video không được để trống.',
            'src.url' => 'Link video phải là một URL hợp lệ.',

            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',

            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',
        ];
    }
}
