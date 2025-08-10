export const JobOrderStatuses = [
  {
    id: 'for viewing',
    label: 'For Viewing',
    badge: 'continuous',
  },
  {
    id: 'for appraisal',
    label: 'For Appraisal',
    badge: 'continuous',
  },
  {
    id: 'for proposal',
    label: 'For Proposal',
    badge: 'continuous',
  },
  {
    id: 'for approval',
    label: 'For Approval',
    badge: 'continuous',
  },
  {
    id: 'for verification',
    label: 'For Verification',
    badge: 'continuous',
  },
  {
    id: 'successful',
    label: 'Successful',
    badge: 'success',
  },
  {
    id: 'failed',
    label: 'Failed',
    badge: 'error',
  },
  {
    id: 'dropped',
    label: 'Dropped',
    badge: 'error',
  },
  {
    id: 'hauling in-progress',
    label: 'Hauling In-Progress',
    badge: 'progress',
  },
  {
    id: 'on-hold',
    label: 'On-hold',
    badge: 'continuous',
  },
  {
    id: 'closed',
    label: 'Closed',
    badge: 'error',
  },
  {
    id: 'completed',
    label: 'Completed',
    badge: 'success',
  },
  {
    id: 'pre-hauling',
    label: 'Pre-Hauling',
    badge: 'continuous',
  },
] as const

type manualStatusUpdaterType = {
  id: JobOrderStatus
  next: {
    tag: JobOrderStatus
    label: string
  }
  stop: {
    tag: JobOrderStatus
    label: string
  }
  description?: string
}

export const manualUpdateStatuses: manualStatusUpdaterType[] = [
  {
    id: 'for viewing',
    next: {
      tag: 'for proposal',
      label: 'Mark as viewed',
    },
    stop: {
      tag: 'dropped',
      label: 'Mark as dropped',
    },
    description:
      'Choose “Mark as viewed” if the business client will proceed to the proposal stage, or “Mark as dropped” if they have decided not to move forward to ocular inspection.',
  },
  {
    id: 'for proposal',
    next: {
      tag: 'successful',
      label: 'Mark as successful',
    },
    stop: {
      tag: 'failed',
      label: 'Mark as failed',
    },
    description:
      'Choose “Mark as successful if the business client accepted the proposal terms and conditions, otherwise “Mark as failed if they have decided not to move forward.',
  },
  {
    id: 'hauling in-progress',
    next: {
      tag: 'for verification',
      label: 'Mark as for verification',
    },
    stop: {
      tag: 'closed',
      label: 'Mark as closed',
    },
    description:
      'Choose “Mark as for verification” if all hauling activities are complete and ready for HR incident report verification, or “Mark as closed” if work cannot proceed due to unforeseen circumstances.',
  },
]

export type JobOrderStatus = (typeof JobOrderStatuses)[number]['id']
