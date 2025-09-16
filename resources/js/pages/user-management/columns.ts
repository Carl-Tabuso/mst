import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { User } from '@/types'
import { Link } from '@inertiajs/vue3'
import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import DataTableHeader from './components/DataTableHeader.vue'
import UserActions from '@/components/user-management/UserDataTableRowActions.vue'
import UserFullNameAndEmail from '@/components/UserFullNameAndEmail.vue'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { format } from 'date-fns'

export const columns: ColumnDef<User>[] = [
  {
    id: 'user',
    meta: { label: 'User' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h(UserFullNameAndEmail, { user: row.original }),
    enableHiding: false
  },
  {
    accessorKey: 'emailVerifiedAt',
    meta: { label: 'Date Verified' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const emailVerifiedAt: string = row.getValue('emailVerifiedAt')

      if (!emailVerifiedAt) {
        return h('div', { class: 'font-medium text-xs text-muted-foreground' }, 'Not verified yet')
      }

      const dateVerified = new Date(emailVerifiedAt)
      const formattedDate = format(dateVerified, 'MMMM dd, yyyy')
      const formattedTime = format(dateVerified, 'hh:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    accessorKey: 'roles',
    meta: { label: 'Role' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      console.log(row.original)
      return h(UserRoleBadge, { roleName: row.original.roles[0].name })
    }
  },
  {
    accessorKey: 'createdAt',
    meta: { label: 'Date Created' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const createdAt = new Date(row.getValue('createdAt'))
      const formattedDate = format(createdAt, 'MMMM dd, yyyy')
      const formattedTime = format(createdAt, 'hh:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    id: 'Action',
    meta: { label: 'Actions' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      try {
        return h(UserActions, { user: row.original })
      } catch (error) {
        return h('div', { class: 'text-xs text-muted-foreground' }, 'N/A')
      }
    },
  },

]