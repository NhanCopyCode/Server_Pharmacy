<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'surname' => 'required|string|max:50',
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phoneNumber' => 'required|string|max:20|unique:users,phoneNumber',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:255',
            'roleId' => 'nullable|integer|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'surname.required' => 'Vui lòng nhập họ.',
            'surname.string' => 'Họ phải là chuỗi ký tự.',
            'surname.max' => 'Họ không được vượt quá 50 ký tự.',

            'name.required' => 'Vui lòng nhập tên.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 50 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',

            'phoneNumber.required' => 'Vui lòng nhập số điện thoại.',
            'phoneNumber.string' => 'Số điện thoại phải là chuỗi.',
            'phoneNumber.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phoneNumber.unique' => 'Số điện thoại đã được sử dụng.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.string' => 'Mật khẩu phải là chuỗi.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',

            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',

            'roleId.integer' => 'Role ID phải là số nguyên.',
            'roleId.exists' => 'Role ID không tồn tại.',
        ];
    }
}
