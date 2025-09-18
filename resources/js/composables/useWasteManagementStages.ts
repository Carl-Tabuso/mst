import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Employee, SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePermissions } from './usePermissions'

export function useWasteManagementStages() {
  const { can } = usePermissions()

  const canUpdateJobOrder = () => computed(() => can('update:job_order'))

  const isForAppraisal = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'for appraisal')
  }

  const isForProposal = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'for proposal')
  }

  const isInProgress = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'in-progress')
  }

  const isSuccessfulProposal = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'successful')
  }

  const isPreHauling = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'pre-hauling')
  }

  const isForStage = (
    jobOrderStatus: JobOrderStatus,
    status: JobOrderStatus,
  ) => {
    return canUpdateJobOrder() && jobOrderStatus === status
  }

  const canUpdateProposalInformation = (jobOrderStatus: JobOrderStatus) => {
    const validStatuses: Array<JobOrderStatus> = [
      'for verification',
      'failed',
      'pre-hauling',
      'in-progress',
      'on-hold',
      'closed',
      'completed',
    ]

    return canUpdateJobOrder() && validStatuses.includes(jobOrderStatus)
  }

  const canUpdateHaulingDuration = (jobOrderStatus: JobOrderStatus) => {
    const validStatus: JobOrderStatus = 'pre-hauling'

    return can('set:hauling_duration') && jobOrderStatus === validStatus
  }

  const canUpdateAppraisalInformation = (
    jobOrderStatus: JobOrderStatus,
    dispatcher: Employee | null,
  ) => {
    const validStatuses: Array<JobOrderStatus> = [
      'for viewing',
      'for proposal',
      'for approval',
      'for verification',
      'pre-hauling',
      'in-progress',
    ]

    const isValidStatus: boolean = validStatuses.includes(jobOrderStatus)
    const isSameDispatcher: boolean =
      dispatcher?.id === usePage<SharedData>().props.auth.user.employee_id
    const hasPermission: boolean = can('assign:appraisers')

    const canAssignAppraisers: boolean = isForAppraisal(jobOrderStatus)
      ? hasPermission
      : isValidStatus && hasPermission && isSameDispatcher

    return canAssignAppraisers
  }

  return {
    isForAppraisal,
    isForProposal,
    isInProgress,
    isSuccessfulProposal,
    isPreHauling,
    isForStage,
    canUpdateProposalInformation,
    canUpdateHaulingDuration,
    canUpdateAppraisalInformation,
  }
}
