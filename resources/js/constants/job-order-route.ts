export const jobOrderRouteNames = [
    {
        id: 'form4',
        route: 'waste_management'
    },
    {
        id: 'it_service',
        route: 'it_service',
    },
    {
        id: 'form5',
        route: 'other'
    }
] as const

export type JobOrderRouteName = typeof jobOrderRouteNames[number]['route'];
