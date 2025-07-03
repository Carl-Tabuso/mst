export interface Employee {
  id: number
  first_name?: string
  middle_name?: string | null
  last_name?: string
  suffix?: string | null
  position_id?: number
  created_at?: string
  updated_at?: string
  full_name?: string
}

export interface User {
  id?: number
  avatar?: string | null
  email?: string
  email_verified_at?: string | null
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
  employee_id?: number
  employee?: Employee
  status?: 'active' | 'deactivated' | 'no_account'
}

export type Position = 'Team Leader' | 'Safety Officer' | 'Human Resource'

export const statuses = [
  { value: 'active', label: 'Active' },
  { value: 'deactivated', label: 'Deactivated' },
  { value: 'no_account', label: 'No Account' },
] as const

export interface UserResponse {
  data: User[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
