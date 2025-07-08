<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inventory' => 'required|integer|min:0',
            'productImageId' => 'nullable|exists:product_images,id',
            'categoryId' => 'required|exists:categories,id',
            'brandId' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'approved' => 'boolean',
            'outstanding' => 'boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề sản phẩm là bắt buộc.',
            'title.string' => 'Tiêu đề sản phẩm phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề sản phẩm không được vượt quá 255 ký tự.',

            'description.string' => 'Mô tả sản phẩm phải là một chuỗi ký tự.',

            'inventory.required' => 'Số lượng tồn kho là bắt buộc.',
            'inventory.integer' => 'Số lượng tồn kho phải là một số nguyên.',
            'inventory.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',

            'productImageId.exists' => 'Hình ảnh sản phẩm được chọn không tồn tại.',

            'categoryId.required' => 'Danh mục sản phẩm là bắt buộc.',
            'categoryId.exists' => 'Danh mục sản phẩm được chọn không tồn tại.',

            'brandId.required' => 'Thương hiệu là bắt buộc.',
            'brandId.exists' => 'Thương hiệu được chọn không tồn tại.',

            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là một số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',

            'approved.boolean' => 'Trạng thái phê duyệt phải là đúng hoặc sai (true/false).',
            'outstanding.boolean' => 'Trạng thái nổi bật phải là đúng hoặc sai (true/false).',

            'main_image.image' => 'Hình ảnh chính phải là một tệp hình ảnh.',
            'main_image.mimes' => 'Hình ảnh chính phải có định dạng: jpeg, png, jpg, gif, hoặc webp.',
            'main_image.max' => 'Kích thước hình ảnh chính không được vượt quá 2MB.',
        ];
    }
}
