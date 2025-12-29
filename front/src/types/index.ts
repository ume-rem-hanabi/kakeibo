// ユーザー
export interface User {
    id: number
    name: string
    email: string
    avatar?: string
}

// カテゴリ
export interface Category {
    id: number
    name: string
    icon?: string
    color?: string
    sort_order: number
}

// 固定費
export interface FixedCost {
    id: number
    user_id: number
    category_id: number
    name: string
    amount: number
    note?: string
    created_at: string
    updated_at: string
    user?: User
    category?: Category
}

// 固定費フォームデータ
export interface FixedCostFormData {
    category_id: number | null
    name: string
    amount: number | null
    note: string
}
