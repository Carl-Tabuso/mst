import { JobOrderStatus } from "@/constants/job-order-statuses";

export function useCorrections() {
    const canCorrectProposalInformation = (status: JobOrderStatus) => {
        const validStatuses: Array<JobOrderStatus> = [
            'for verification',
            'failed',
            'pre-hauling',
            'hauling in-progress',
            'on-hold',
            'closed',
            'completed',
        ]

        return validStatuses.includes(status)
    }

    return {
        canCorrectProposalInformation
    }
}