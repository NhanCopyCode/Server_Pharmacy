<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép mọi request (tuỳ vào auth bạn có thể chỉnh)
    }

    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'approved' => 'nullable|boolean',
            'position_id'  => 'required|exists:banner_positions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.string'   => 'Tiêu đề phải là chuỗi.',
            'title.max'      => 'Tiêu đề không được vượt quá 255 ký tự.',

            'image.required' => 'Vui lòng chọn hình ảnh.',
            'image.image'    => 'File phải là hình ảnh.',
            'image.mimes'    => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'image.max'      => 'Hình ảnh không được vượt quá 2MB.',

            'approved.boolean' => 'Trường duyệt phải là true hoặc false.',

            'position_id.required' => 'Vị trí hiển thị là bắt buộc.',
            'position_id.exists'   => 'Vị trí hiển thị không hợp lệ.',
        ];
    }
}
