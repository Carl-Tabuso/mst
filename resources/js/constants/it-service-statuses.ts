export const ItServiceStatuses = [
    { id: 'for check up', label: 'For Check Up', badge: 'secondary' },
    { id: 'for final service', label: 'For Final Service', badge: 'progress' },
    { id: 'completed', label: 'Completed', badge: 'success' },
] as const

export type ItServiceStatus = (typeof ItServiceStatuses)[number]['id']