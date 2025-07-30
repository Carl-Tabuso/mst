import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { computed } from 'vue'

export function useJobOrderStatus() {
  const getCancelledStatuses = computed(() => {
    const cancelledStatuses = ['dropped', 'closed', 'failed']

    return JobOrderStatuses.filter((status) =>
      cancelledStatuses.includes(status.id),
    )
  })

  return { getCancelledStatuses }
}
