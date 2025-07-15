<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
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
            'title'   => 'required|string|max:255',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content' => 'required|string',
            'approved'  => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required'   => 'Tiêu đề không được để trống.',
            'title.string'     => 'Tiêu đề phải là chuỗi.',
            'title.max'        => 'Tiêu đề không được vượt quá 255 ký tự.',

            'image.image'      => 'Ảnh phải là tệp hình ảnh.',
            'image.mimes'      => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'image.max'        => 'Ảnh không được lớn hơn 2MB.',

            'content.required' => 'Nội dung không được để trống.',
            'content.string'   => 'Nội dung phải là chuỗi.',

            'approved.required'  => 'Trạng thái kích hoạt là bắt buộc.',
            'approved.boolean'   => 'Trạng thái kích hoạt phải là true hoặc false.',
        ];
    }
}
