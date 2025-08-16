import { BadgeVariants } from '@/components/ui/badge'

interface CorrectionStatus {
  id: 'Pending' | 'Approved' | 'Rejected'
  badge: BadgeVariants['variant']
}

export const correctionStatuses: CorrectionStatus[] = [
  {
    id: 'Pending',
    badge: 'continuous',
  },
  {
    id: 'Approved',
    badge: 'success',
  },
  {
    id: 'Rejected',
    badge: 'destructive',
  },
]
