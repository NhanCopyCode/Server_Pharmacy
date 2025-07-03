<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'approved' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên hãng là bắt buộc.',
            'name.string' => 'Tên hãng phải là chuỗi.',
            'name.max' => 'Tên hãng không được vượt quá 255 ký tự.',

            'description.required' => 'Mô tả là bắt buộc.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'logo.required' => 'Logo là bắt buộc.',
            'logo.image' => 'Logo phải là một hình ảnh.',
            'logo.mimes' => 'Logo phải có định dạng jpeg, png, jpg, gif hoặc svg,webp.',
            'logo.max' => 'Logo không được lớn hơn 2MB.',


            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',
        ];
    }
}
