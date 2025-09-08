import { Employee } from '@/types'
export interface EmployeeResponse {
  data: Employee[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from?: number
  to?: number
  path?: string
  links?: {
    url: string | null
    label: string
    active: boolean
  }[]
}
