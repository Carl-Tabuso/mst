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
    label: 'Hauling In-progress',
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
    badge: 'secondary',
  },
  {
    id: 'completed',
    label: 'Completed',
    badge: 'success',
  },
] as const

export type JobOrderStatus = (typeof JobOrderStatuses)[number]['id']
