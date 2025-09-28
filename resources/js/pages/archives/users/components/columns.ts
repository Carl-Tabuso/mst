import { Checkbox } from '@/components/ui/checkbox'
import UserFullNameAndEmail from '@/components/UserFullNameAndEmail.vue'
import UserRoleBadge from '@/components/UserRoleBadge.vue'
import { User } from '@/types'
import { ColumnDef } from '@tanstack/vue-table'
import { format } from 'date-fns'
import { h } from 'vue'
import DataTableHeader from './DataTableHeader.vue'
import ForceDeleteUser from './ForceDeleteUser.vue'
import RestoreUser from './RestoreUser.vue'

export const columns: ColumnDef<User>[] = [
  {
    id: 'select',
    header: ({ table }) =>
      h(Checkbox, {
        checked: table.getIsAllPageRowsSelected(),
        'onUpdate:checked': (value: boolean) =>
          table.toggleAllPageRowsSelected(!!value),
        ariaLabel: 'Select all',
      }),
    cell: ({ row }) =>
      h(Checkbox, {
        checked: row.getIsSelected(),
        'onUpdate:checked': (value: boolean) => row.toggleSelected(!!value),
        ariaLabel: 'Select row',
      }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    id: 'user',
    meta: { label: 'User' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h(UserFullNameAndEmail, { user: row.original }),
    enableHiding: false,
  },
  {
    accessorKey: 'emailVerifiedAt',
    meta: { label: 'Date Verified' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateVerified = new Date(row.getValue('emailVerifiedAt'))
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
      return h(UserRoleBadge, { roleName: row.original.roles[0].name })
    },
  },
  {
    accessorKey: 'createdAt',
    meta: { label: 'Date Created' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateCreated = new Date(row.getValue('createdAt'))
      const formattedDate = format(dateCreated, 'MMMM dd, yyyy')
      const formattedTime = format(dateCreated, 'hh:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    accessorKey: 'deletedAt',
    meta: { label: 'Date Archived' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateArchived = new Date(row.getValue('deletedAt'))
      const formattedDate = format(dateArchived, 'MMMM dd, yyyy')
      const formattedTime = format(dateArchived, 'hh:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      return h('div', { class: 'flex flex-row items-center gap-3' }, [
        h(RestoreUser, { user: row.original }),
        h(ForceDeleteUser, { user: row.original }),
      ])
    },
    enableHiding: false,
  },
]
