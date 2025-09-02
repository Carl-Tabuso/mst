export const machineStatuses = [
    {
        id: 'complete repair',
        label: 'Complete Repair'
    },
    {
        id: 'pending repair',
        label: 'Pending Repair'
    },
    {
        id: 'pull out',
        label: 'For Pull Out / Shop Repair'
    },
    {
        id: 'irrepairable',
        label: 'Irrepairable'
    },
    {
        id: 'others',
        label: 'Others'
    },
] as const

export type MachineStatusType = (typeof machineStatuses)[number]['id']
