export const haulingStatuses = [
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
    id: 'in progress',
    label: 'In-Progress',
    badge: 'progress',
  },
  {
    id: 'done',
    label: 'Done',
    badge: 'success',
  },
] as const

export type HaulingStatusType = (typeof haulingStatuses)[number]['id']
