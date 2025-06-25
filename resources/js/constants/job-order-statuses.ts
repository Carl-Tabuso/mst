export const JobOrderStatuses = [
  {
    id: 'for viewing',
    label: 'For Viewing',
  },
  {
    id: 'for appraisal',
    label: 'For Appraisal',
  },
  {
    id: 'for proposal',
    label: 'For Proposal',
  },
  {
    id: 'for approval',
    label: 'For Approval',
  },
  {
    id: 'for personnel assignment',
    label: 'For Personnel Assignment',
  },
  {
    id: 'for safety inspection',
    label: 'For Safety Inspection',
  },
  {
    id: 'for verification',
    label: 'For Verification',
  },
  {
    id: 'verified',
    label: 'Verified',
  },
  {
    id: 'successful',
    label: 'Successful',
  },
  {
    id: 'failed',
    label: 'Failed'
  },
  {
    id: 'dropped',
    label: 'Dropped'   
  },
  {
    id: 'hauling in-progress',
    label: 'Hauling In-progress'
  },
  {
    id: 'on-hold',
    label: 'On-hold'   
  },
  {
    id: 'closed',
    label: 'Closed'  
  },
  {
    id: 'completed',
    label: 'Completed'
  }
] as const

export type JobOrderStatus = typeof JobOrderStatuses[number]['id'];