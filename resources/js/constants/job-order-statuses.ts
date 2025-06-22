
export const JobOrderStatuses = {
  'for viewing': 'For Viewing',
  'for appraisal': 'For Appraisal',
  'for proposal': 'For Proposal',
  'for approval': 'For Approval',
  'for personnel assignment': 'For Personnel Assignment',
  'for safety inspection': 'For Safety Inspection',
  'for verification': 'For Verification',
  'verified': 'Verified',
  'successful': 'Successful',
  'failed': 'Failed',
  'dropped': 'Dropped',
  'hauling in-progress': 'Hauling In-progress',
  'on-hold': 'On-hold',
  'closed': 'Closed',
  'completed': 'Completed',
} as const;

export type JobOrderStatus = keyof typeof JobOrderStatuses;