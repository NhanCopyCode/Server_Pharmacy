<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',

            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.exists' => 'Email chưa được đăng ký.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ];
    }
}
