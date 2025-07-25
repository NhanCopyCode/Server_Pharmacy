<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'approved' => 'nullable|boolean',
            'userId' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'post_category_id' => 'required|exists:post_categories,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.string' => 'Tiêu đề phải là chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'description.required' => 'Mô tả không được để trống.',
            'description.string' => 'Mô tả phải là chuỗi.',

            'userId.required' => 'Người dùng là bắt buộc.',
            'userId.exists' => 'Người dùng không tồn tại trong hệ thống.',

            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',

            'image.image' => 'Tệp tải lên phải là ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',

            'post_category_id.required' => 'Danh mục bài viết là bắt buộc.',
            'post_category_id.exists'   => 'Danh mục bài viết không tồn tại.',

        ];
    }
}
