import { UserRoleType } from './user-role'

type RoleType = {
  id: 'teamLeader' | 'teamMechanic' | 'safetyOfficer' | 'teamDriver'
  label: string
  type: UserRoleType | UserRoleType[]
}

export const haulingRoles: RoleType[] = [
  {
    id: 'teamLeader',
    label: 'Team Leader',
    type: 'team leader',
  },
  {
    id: 'teamMechanic',
    label: 'Mechanic',
    type: 'regular',
  },
  {
    id: 'safetyOfficer',
    label: 'Safety Officer',
    type: ['team leader', 'safety officer'],
  },
] as const

export type HaulingRoleType = (typeof haulingRoles)[number]['id']
