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
    id: 'in-progress',
    label: 'In-Progress',
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
  {
    id: 'for check up',
    label: 'For Checkup',
    badge: 'continuous',
  },
  {
    id: 'for final service',
    label: 'For Final Service',
    badge: 'continuous',
  },
] as const

type manualStatusUpdaterType = {
  id: JobOrderStatus
  next: {
    tag: JobOrderStatus
    label: string
    description: string
  }
  stop: {
    tag: JobOrderStatus
    label: string
    description: string
  }
}

export const manualUpdateStatuses: manualStatusUpdaterType[] = [
  {
    id: 'for viewing',
    next: {
      tag: 'for proposal',
      label: 'Mark as viewed',
      description:
        'This will mark the ticket as For Proposal and will proceed to the proposal stage.',
    },
    stop: {
      tag: 'dropped',
      label: 'Mark as dropped',
      description:
        'This will mark the ticket as Dropped, indicating the client did not want to proceed with the ocular inspection.',
    },
  },
  {
    id: 'for proposal',
    next: {
      tag: 'successful',
      label: 'Mark as successful',
      description:
        'This will mark the ticket as Successful, indicating the client accepted the proposal terms and conditions.',
    },
    stop: {
      tag: 'failed',
      label: 'Mark as failed',
      description:
        'This will mark the ticket as Failed, indicating the client did not accept the proposal or chose not to proceed.',
    },
  },
  {
    id: 'in-progress',
    next: {
      tag: 'for verification',
      label: 'Mark as for verification',
      description:
        'This will mark the ticket as For Verification, signaling that all hauling activities are complete and ready for validation.',
    },
    stop: {
      tag: 'closed',
      label: 'Mark as closed',
      description:
        'This will permanently close the ticket, indicating that the work cannot proceed or has been concluded prematurely.',
    },
  },
]

export type JobOrderStatus = (typeof JobOrderStatuses)[number]['id']
