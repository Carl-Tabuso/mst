import { correctionStatuses } from '@/constants/correction-statuses'
import { ItServiceStatuses } from '@/constants/it-service-statuses'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { userRoles } from '@/constants/user-role'

export function useJobOrderDicts() {
  const statusMap = Object.fromEntries(
    JobOrderStatuses.map((status) => [status.id, status]),
  )

  const routeMap = Object.fromEntries(
    jobOrderRouteNames.map((route) => [route.id, route.route]),
  )

  const correctionStatusMap = Object.fromEntries(
    correctionStatuses.map((status) => {
      return [status.id, status]
    }),
  )

  const userRoleMap = Object.fromEntries(
    userRoles.map((role) => {
      return [role.id, role]
    }),
  )

  return {
    statusMap,
    routeMap,
    correctionStatusMap,
    userRoleMap,
  }
}
