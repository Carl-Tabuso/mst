import { UserRoleType } from '@/constants/user-role'
import { Employee } from '@/types'

export interface GroupedEmployeesByAccountRole {
  role: UserRoleType
  items: Employee[]
}
