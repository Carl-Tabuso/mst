import { jobOrderRouteNames } from '@/constants/job-order-route'
import { JobOrderStatuses } from '@/constants/job-order-statuses'

export function useJobOrderDicts() {
  const statusMap = Object.fromEntries(
    JobOrderStatuses.map((status) => [status.id, status]),
  )

  const routeMap = Object.fromEntries(
    jobOrderRouteNames.map((route) => [route.id, route.route]),
  )

  return {
    statusMap,
    routeMap,
  }
}
