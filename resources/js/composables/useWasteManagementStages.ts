import { computed } from "vue"
import { usePermissions } from "./usePermissions"
import { JobOrder } from "@/types"
import { JobOrderStatus } from "@/constants/job-order-statuses"

export function useWasteManagementStages() {
    const { can } = usePermissions()

    const canUpdateJobOrder = () => computed(() => can('update:job_order'))

    const isForAppraisal = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'for appraisal')
    }

    const isForProposal = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'for proposal')
    }

    const isForPersonnelAssignment = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'for personnel assignment')
    }

    const isForSafetyInspection = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'for safety inspection')
    }

    const isForHaulingInProgress = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'hauling in-progress')
    }

    const isSuccessfulProposal = (jobOrder: JobOrder) => {
        return isForStage(jobOrder, 'successful')
    }

    const isForStage = (jobOrder: JobOrder, status: JobOrderStatus) => {
        return canUpdateJobOrder() && jobOrder.status === status
    }

    const canUpdateProposalInformation = (jobOrderStatus: JobOrderStatus) => {
        const validStatuses = [
            'for personnel assignment',
            'for safety inspection',
            'for verification',
            'verified',
            'successful',
            'failed',
            'dropped',
            'hauling in-progress',
            'on-hold',
            'closed',
            'completed'
        ];

        return canUpdateJobOrder() && validStatuses.includes(jobOrderStatus)
    }

    const canUpdateHaulingDuration = (jobOrderStatus: JobOrderStatus) => {
        const validStatuses = [
            'successful',
            'for personnel assignment',
            'for safety inspection',
        ];

        return canUpdateJobOrder() && can('set:hauling_duration') && validStatuses.includes(jobOrderStatus)
    }

    return {
        isForAppraisal,
        isForProposal,
        isForPersonnelAssignment,
        isForSafetyInspection,
        isForHaulingInProgress,
        isSuccessfulProposal,
        isForStage,
        canUpdateProposalInformation,
        canUpdateHaulingDuration,
    }
}