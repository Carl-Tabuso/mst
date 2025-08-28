import { JobOrderStatus } from '@/constants/job-order-statuses'

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
