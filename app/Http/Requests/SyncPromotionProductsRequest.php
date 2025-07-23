<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncPromotionProductsRequest extends FormRequest
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

  
    public function rules()
    {
        return [
            'promotion_id' => 'required|exists:promotions,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'promotion_id.required' => 'Trường khuyến mãi là bắt buộc.',
            'promotion_id.exists' => 'Khuyến mãi không tồn tại.',

            'product_ids.array' => 'Danh sách sản phẩm không hợp lệ.',
            'product_ids.*.integer' => 'ID sản phẩm phải là số nguyên.',
            'product_ids.*.exists' => 'Một hoặc nhiều sản phẩm không tồn tại.',

            'category_ids.array' => 'Danh sách danh mục không hợp lệ.',
            'category_ids.*.integer' => 'ID danh mục phải là số nguyên.',
            'category_ids.*.exists' => 'Một hoặc nhiều danh mục không tồn tại.',
        ];
    }
}
