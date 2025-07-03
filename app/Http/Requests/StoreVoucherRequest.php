<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:vouchers,code'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'max_discount_value' => [
                'nullable',
                Rule::requiredIf($this->discount_type === 'percent'),
                'numeric',
                'min:0',
            ],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'applies_to' => ['required', Rule::in(['order', 'product', 'category'])],
            'approved' => ['nullable', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên mã giảm giá.',
            'title.max' => 'Tên mã giảm giá không được vượt quá 255 ký tự.',
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'discount_type.required' => 'Vui lòng chọn loại giảm.',
            'discount_type.in' => 'Loại giảm không hợp lệ.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn hoặc bằng 0.',
            'max_discount_value.required' => 'Giảm tối đa là bắt buộc khi giảm theo phần trăm.',
            'max_discount_value.numeric' => 'Giảm tối đa phải là số.',
            'max_discount_value.min' => 'Giảm tối đa phải lớn hơn hoặc bằng 0.',
            'usage_limit.integer' => 'Giới hạn lượt sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn lượt sử dụng phải lớn hơn hoặc bằng 0.',
            'usage_limit_per_user.integer' => 'Giới hạn mỗi người phải là số nguyên.',
            'usage_limit_per_user.min' => 'Giới hạn mỗi người phải lớn hơn hoặc bằng 0.',
            'min_order_value.numeric' => 'Giá trị tối thiểu phải là số.',
            'min_order_value.min' => 'Giá trị tối thiểu phải lớn hơn hoặc bằng 0.',
            'applies_to.required' => 'Vui lòng chọn kiểu áp dụng.',
            'applies_to.in' => 'Kiểu áp dụng không hợp lệ.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'description' => $this->input('description') ?: null,
            'max_discount_value' => $this->input('max_discount_value') ?: null,
            'min_order_value' => $this->input('min_order_value') ?: null,
            'usage_limit' => $this->input('usage_limit') ?: null,
            'usage_limit_per_user' => $this->input('usage_limit_per_user') ?: null,
            'applies_to' => $this->input('applies_to') ?: null,
        ]);
    }
}
