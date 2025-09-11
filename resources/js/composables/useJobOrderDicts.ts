import { correctionStatuses } from '@/constants/correction-statuses'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { machineStatuses } from '@/constants/machine-statuses'
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

  const machineStatusMap = Object.fromEntries(
    machineStatuses.map((status) => {
      return [status.id, status]
    }),
  )

  return {
    statusMap,
    routeMap,
    correctionStatusMap,
    userRoleMap,
    machineStatusMap,
  }
}
