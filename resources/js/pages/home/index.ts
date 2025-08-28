import { ServiceType, User } from '@/types'
import { FunctionalComponent } from 'vue'

export type GreetingKey = 'morning' | 'afternoon' | 'evening'

export type Status = 'Active' | 'Inactive' | 'No Account'

export interface RecentActivities {
  id: number
  humanDiff: string
  description: string
  causer: User
}

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

export interface CurrentYearParticipation {
  month: string
  'Waste Management': number
  'IT Service': number
  'Other Services': number
}

export interface ServiceTypeCardBindings {
  'Waste Management': {
    icon: FunctionalComponent
    bgClass: string
    textClass: string
  }
  'IT Service': {
    icon: FunctionalComponent
    bgClass: string
    textClass: string
  }
  'Other Services': {
    icon: FunctionalComponent
    bgClass: string
    textClass: string
  }
}
