import { BadgeVariants } from '@/components/ui/badge'

interface CorrectionStatus {
  id: 'pending' | 'approved' | 'rejected'
  label: 'Pending' | 'Approved' | 'Rejected'
  badge: BadgeVariants['variant']
}

export const correctionStatuses: CorrectionStatus[] = [
  {
    id: 'pending',
    label: 'Pending',
    badge: 'continuous',
  },
  {
    id: 'approved',
    label: 'Approved',
    badge: 'success',
  },
  {
    id: 'rejected',
    label: 'Rejected',
    badge: 'error',
  },
] as const

export type CorrectionStatusType = (typeof correctionStatuses)[number]['id']
