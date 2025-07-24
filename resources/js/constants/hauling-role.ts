export const haulingRoles = [
  {
    id: 'teamLeader',
    label: 'Team Leader',
  },
  {
    id: 'teamMechanic',
    label: 'Mechanic',
  },
  {
    id: 'safetyOfficer',
    label: 'Safety Officer',
  },
  {
    id: 'teamDriver',
    label: 'Driver',
  },
] as const

export type HaulingRoleType = (typeof haulingRoles)[number]['id']
