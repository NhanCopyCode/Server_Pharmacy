<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerPositionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'approved' => 'nullable|boolean',
            'position_id' => 'nullable|integer|exists:banner_positions,id',      'position_id.integer'  => 'ID vị trí phải là số nguyên.',
            'position_id.exists'   => 'ID vị trí không hợp lệ.',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên vị trí không được để trống.',
            'name.string' => 'Tên vị trí phải là chuỗi.',
            'name.max' => 'Tên vị trí không được vượt quá 255 ký tự.',
            'approved.boolean' => 'Trạng thái duyệt phải là true hoặc false.',
            'position_id.integer'  => 'ID vị trí phải là số nguyên.',
            'position_id.exists'   => 'ID vị trí không hợp lệ.',
        ];
    }
}
