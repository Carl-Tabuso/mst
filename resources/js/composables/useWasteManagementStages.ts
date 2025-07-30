import { JobOrderStatus } from '@/constants/job-order-statuses'
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

  const isHaulingInProgress = (jobOrderStatus: JobOrderStatus) => {
    return isForStage(jobOrderStatus, 'hauling in-progress')
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
      'successful',
      'failed',
      'pre-hauling',
      'hauling in-progress',
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

  return {
    isForAppraisal,
    isForProposal,
    isHaulingInProgress,
    isSuccessfulProposal,
    isPreHauling,
    isForStage,
    canUpdateProposalInformation,
    canUpdateHaulingDuration,
  }
}
