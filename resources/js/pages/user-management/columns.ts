import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { User } from '@/types'
import { Link } from '@inertiajs/vue3'
import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'
import DataTableHeader from '@/pages/job-orders/components/DataTableHeader.vue'
import UserActions from '@/components/user-management/UserDataTableRowActions.vue'

export const columns: ColumnDef<User>[] = [

  {
    accessorKey: 'name',
    meta: { label: 'Name' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const employee = row.original.employee
      const name = employee 
        ? `${employee.firstName} ${employee.lastName}`.trim()
        : 'N/A'
      
      const userId = row.original?.id

      if (!userId) {
        return h('div', { class: 'text-sm font-medium' }, name)
      }

      return h(
        'div',
        { class: 'text-sm font-medium' },
        name
      )
    },
    enableHiding: false,
  },
  {
    accessorKey: 'email',
    meta: { label: 'Email' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const email = row.original.email || 'N/A'
      return h('div', { class: 'text-xs' }, email)
    },
  },
  {
    accessorKey: 'created_at',
    meta: { label: 'Date Created' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const createdAt = row.original.createdAt
      if (!createdAt)
        return h(
          'div',
          { class: 'text-xs text-muted-foreground' },
          'N/A'
        )

      try {
        const formattedDate = new Date(createdAt as string).toLocaleDateString(
          'en-PH',
          {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
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
  accessorKey: 'status',
  meta: { label: 'Status' },
  header: ({ column }) => h(DataTableHeader, { column: column }),
  cell: ({ row }) => {
    const status = row.original.deletedAt ? 'inactive' : 'active'
    const variant = status === 'active' ? 'default' : 'destructive'

    return h(
      Badge,
      { variant },
      () => status.charAt(0).toUpperCase() + status.slice(1)
    )
  },
},

  {
    accessorKey: 'Action',
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