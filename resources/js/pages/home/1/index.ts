import { JobOrderStatus } from '@/constants/job-order-statuses'
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
  status: JobOrderStatus
  humanDiff: string
  frontliner: string
}

export interface AwaitingCorrectionReviewsCard {
  ticket: string
  serviceType: string
  requestedAt: string
  changesCount: string
  requestedBy: string
}

export interface AgingJobOrderOrdersCard {
  ticket: string
  serviceType: string
  status: JobOrderStatus
  lastUpdated: string
  frontliner: string
}
