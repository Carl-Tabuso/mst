import { JobOrderStatus } from '@/constants/job-order-statuses'
import { User } from '@/types'

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
  id: number
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
