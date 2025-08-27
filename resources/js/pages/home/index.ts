import { ServiceType } from '@/types'

export type GreetingKey = 'morning' | 'afternoon' | 'evening'

export type Status = 'Active' | 'Inactive' | 'No Account'

export interface EmployeeStatistics {
  status: Status
  total: number
}

export interface JobOrderServiceTypeCards {
  serviceType: ServiceType
  total: number
}

export interface MyRecentActivites {
  id: number
  description: string
  ipAddress: string
  browser: string
  platform: string
  timestamp: string
}
