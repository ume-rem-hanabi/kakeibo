<?php

namespace App\Http\Requests\FixedCost;

use Illuminate\Foundation\Http\FormRequest;

class StoreFixedCostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 認証はミドルウェアで行うのでtrue
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'カテゴリ',
            'name' => '項目名',
            'amount' => '金額',
            'note' => '備考',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'カテゴリを選択してください',
            'category_id.exists' => '選択されたカテゴリが存在しません',
            'name.required' => '項目名を入力してください',
            'name.max' => '項目名は255文字以内で入力してください',
            'amount.required' => '金額を入力してください',
            'amount.integer' => '金額は整数で入力してください',
            'amount.min' => '金額は0円以上で入力してください',
            'note.max' => '備考は1000文字以内で入力してください',
        ];
    }
}
