<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'userId' => 'sometimes|required|exists:users,id',
            'approved' => 'nullable|boolean',
            'post_category_id' => 'required|exists:post_categories,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề không được để trống khi cập nhật.',
            'title.string' => 'Tiêu đề phải là chuỗi.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'description.required' => 'Mô tả không được để trống khi cập nhật.',
            'description.string' => 'Mô tả phải là chuỗi.',

            'userId.required' => 'Người dùng là bắt buộc khi cập nhật.',
            'userId.exists' => 'Người dùng không tồn tại trong hệ thống.',

            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',

            'post_category_id.required' => 'Danh mục bài viết là bắt buộc.',
            'post_category_id.exists'   => 'Danh mục bài viết không tồn tại.',
        ];
    }
}
