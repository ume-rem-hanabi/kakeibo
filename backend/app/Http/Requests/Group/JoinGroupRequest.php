<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class JoinGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invitation_code' => 'required|string|exists:groups,invitation_code',
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'invitation_code.required' => '招待コードは必須です。',
            'invitation_code.string' => '招待コードは文字列で入力してください。',
            'invitation_code.exists' => '招待コードが見つかりません。',
        ];
    }

    /**
     * 属性名の日本語化
     */
    public function attributes(): array
    {
        return [
            'invitation_code' => '招待コード',
        ];
    }
}
