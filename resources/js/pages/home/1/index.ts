import { ServiceType, User } from '@/types'

export type Status = 'Active' | 'Inactive' | 'No Account'

export interface EmployeeStatistics {
  status: Status
  total: number
}

export interface JobOrderServiceTypeCards {
  serviceType: ServiceType
  total: number
}

export interface RecentActivitiesCard {
  id: number
  humanDiff: string
  description: string
  causer: User
}

export interface RecentJobOrders {
  ticket: string
  serviceType: string
  status: string
  humanDiff: string
  frontliner: string
}
