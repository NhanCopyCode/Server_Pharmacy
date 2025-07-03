<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image',
            'parentId' => [
                'nullable',
            ],
            'outstanding' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc khi cập nhật.',
            'name.string' => 'Tên danh mục phải là chuỗi.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'parentId.exists' => 'Danh mục cha không hợp lệ.',
            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',
            'outstanding.boolean' => 'Trạng thái duyệt phải là true hoặc false.',

        ];
    }
}
