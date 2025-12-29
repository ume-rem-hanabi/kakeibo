/**
 * 共通バリデーションルール
 */

export const rules = {
    // 必須チェック
    required: (v: any) => !!v || '必須項目です',

    // 必須チェック（0を許可）
    requiredWithZero: (v: any) => (v !== null && v !== undefined && v !== '') || '必須項目です',

    // 数値チェック（0以上）
    number: (v: any) => {
        if (v === null || v === '') return '必須項目です'
        return v >= 0 || '0以上を入力してください'
    },

    // 数値チェック（1以上）
    positiveNumber: (v: any) => {
        if (v === null || v === '') return '必須項目です'
        return v > 0 || '1以上を入力してください'
    },

    // メールアドレス
    email: (v: string) => {
        if (!v) return true
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return pattern.test(v) || '有効なメールアドレスを入力してください'
    },

    // 最小文字数
    minLength: (min: number) => (v: string) => {
        if (!v) return true
        return v.length >= min || `${min}文字以上で入力してください`
    },

    // 最大文字数
    maxLength: (max: number) => (v: string) => {
        if (!v) return true
        return v.length <= max || `${max}文字以内で入力してください`
    },

    // 文字数範囲
    lengthRange: (min: number, max: number) => (v: string) => {
        if (!v) return true
        return (v.length >= min && v.length <= max) || `${min}〜${max}文字で入力してください`
    },

    // URL
    url: (v: string) => {
        if (!v) return true
        try {
            new URL(v)
            return true
        } catch {
            return '有効なURLを入力してください'
        }
    },

    // 電話番号（ハイフンなし）
    phone: (v: string) => {
        if (!v) return true
        const pattern = /^0\d{9,10}$/
        return pattern.test(v) || '有効な電話番号を入力してください'
    },

    // 郵便番号（ハイフンなし）
    zipCode: (v: string) => {
        if (!v) return true
        const pattern = /^\d{7}$/
        return pattern.test(v) || '有効な郵便番号を入力してください（7桁）'
    },

    // 半角英数字
    alphanumeric: (v: string) => {
        if (!v) return true
        const pattern = /^[a-zA-Z0-9]+$/
        return pattern.test(v) || '半角英数字で入力してください'
    },

    // パスワード（8文字以上、英数字含む）
    password: (v: string) => {
        if (!v) return '必須項目です'
        if (v.length < 8) return '8文字以上で入力してください'
        if (!/[a-zA-Z]/.test(v)) return '英字を含めてください'
        if (!/[0-9]/.test(v)) return '数字を含めてください'
        return true
    },

    // 同じ値かチェック
    sameAs:
        (target: any, label: string = '値') =>
        (v: any) => {
            return v === target || `${label}が一致しません`
        },

    // カスタムルール作成ヘルパー
    custom: (validator: (v: any) => boolean, message: string) => (v: any) => {
        return validator(v) || message
    },
}

// 複数ルールを組み合わせるヘルパー
export function combineRules(...ruleFns: Array<(v: any) => boolean | string>) {
    return (v: any) => {
        for (const rule of ruleFns) {
            const result = rule(v)
            if (result !== true) return result
        }
        return true
    }
}

export default rules
