import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { Employee } from '@/types'
import { Link } from '@inertiajs/vue3'
import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import DataTableHeader from './DataTableHeader.vue'
import EmployeeActions from './EmployeeActions.vue'

export const columns: ColumnDef<Employee>[] = [
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
    accessorKey: 'fullName',
    meta: { label: 'Full Name' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const fullName = row.getValue('fullName') || 'N/A'
      const employeeId = row.original?.id

      if (!employeeId) {
        return h('div', { class: 'text-sm font-medium' }, fullName)
      }

      const getEditRoute = () => {
        try {
          return route('employee-management.edit', employeeId)
        } catch (error) {
          console.error('Error generating edit route:', error)
          return '#'
        }
      }
      const getProfileRoute = () => {
        try {
          return route('employees.profile.show', employeeId)
        } catch (error) {
          console.error('Error generating edit route:', error)
          return '#'
        }
      }

      return h(
        Link,
        {
          href: getProfileRoute(),
          class: 'text-primary underline hover:opacity-80 text-sm font-medium',
        },
        () => fullName,
      )
    },
    enableHiding: false,
  },
  {
    accessorKey: 'positionName',
    meta: { label: 'Position' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const positionName = row.getValue('positionName') || 'N/A'
      return h('div', { class: 'text-xs font-medium' }, positionName)
    },
  },
  {
    accessorKey: 'email',
    meta: { label: 'Email' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const email = row.getValue('email') || 'N/A'
      return h('div', { class: 'text-xs' }, email)
    },
  },
  {
    accessorKey: 'accountStatus',
    meta: { label: 'Account Status' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const status = row.getValue('accountStatus') || 'No Account'
      const variant = status === 'Active' ? 'default' : 'destructive'

      return h(Badge, { variant }, () => status)
    },
  },
  {
    accessorKey: 'accountCreatedAt',
    meta: { label: 'Account Created' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const createdAt = row.getValue('accountCreatedAt')
      if (!createdAt)
        return h(
          'div',
          { class: 'text-xs text-muted-foreground' },
          'No Account',
        )

      try {
        const formattedDate = new Date(createdAt as string).toLocaleDateString(
          'en-PH',
          {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          },
        )
        return h('div', { class: 'text-xs' }, formattedDate)
      } catch (error) {
        return h(
          'div',
          { class: 'text-xs text-muted-foreground' },
          'Invalid date',
        )
      }
    },
  },
  {
    accessorKey: 'Action',
    meta: { label: 'Actions' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      try {
        return h(EmployeeActions, { employee: row.original })
      } catch (error) {
        return h('div', { class: 'text-xs text-muted-foreground' }, 'N/A')
      }
    },
  },
  {
    id: 'actions',
    header: () => h('div', { class: 'text-xs font-medium pl-4' }, 'Actions'),
    meta: { label: 'Actions' },

    enableHiding: false,
    enableSorting: false,
  },
]
