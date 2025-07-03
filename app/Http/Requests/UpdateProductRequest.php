<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'inventory' => 'sometimes|required|integer|min:0',
            'productImageId' => 'nullable|exists:product_images,id',
            'categoryId' => 'sometimes|required|exists:categories,id',
            'brandId' => 'sometimes|required|exists:brands,id',
            'price' => 'sometimes|required|numeric|min:0',
            'approved' => 'boolean',
            'outstanding' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề sản phẩm là bắt buộc nếu được cung cấp.',
            'title.string' => 'Tiêu đề sản phẩm phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề sản phẩm không được vượt quá 255 ký tự.',

            'description.string' => 'Mô tả sản phẩm phải là một chuỗi ký tự.',

            'inventory.integer' => 'Số lượng tồn kho phải là một số nguyên.',
            'inventory.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',

            'productImageId.exists' => 'Hình ảnh sản phẩm được chọn không tồn tại.',

            'categoryId.exists' => 'Danh mục sản phẩm được chọn không tồn tại.',
            'brandId.exists' => 'Thương hiệu được chọn không tồn tại.',

            'price.numeric' => 'Giá sản phẩm phải là một số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',

            'approved.boolean' => 'Trạng thái phê duyệt phải là đúng hoặc sai (true/false).',
            'outstanding.boolean' => 'Trạng thái phê duyệt phải là đúng hoặc sai (true/false).',
        ];
    }
}
