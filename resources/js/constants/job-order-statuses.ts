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
    id: 'for personnel assignment',
    label: 'For Personnel Assignment',
    badge: 'continuous',
  },
  {
    id: 'for safety inspection',
    label: 'For Safety Inspection',
    badge: 'continuous',
  },
  {
    id: 'for verification',
    label: 'For Verification',
    badge: 'continuous',
  },
  {
    id: 'verified',
    label: 'Verified',
    badge: 'success',
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
      'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad reprehenderit aperiam ullam mollitia repellendus dolor dolores vero, laudantium explicabo voluptatibus velit eaque ut odit nihil qui est reiciendis alias ipsam?',
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
      'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad reprehenderit aperiam ullam mollitia repellendus dolor dolores vero, laudantium explicabo voluptatibus velit eaque ut odit nihil qui est reiciendis alias ipsam?',
  },
  {
    id: 'hauling in-progress',
    next: {
      tag: 'completed',
      label: 'Mark as completed',
    },
    stop: {
      tag: 'closed',
      label: 'Mark as closed',
    },
    description:
      'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad reprehenderit aperiam ullam mollitia repellendus dolor dolores vero, laudantium explicabo voluptatibus velit eaque ut odit nihil qui est reiciendis alias ipsam?',
  },
]

export type JobOrderStatus = (typeof JobOrderStatuses)[number]['id']
