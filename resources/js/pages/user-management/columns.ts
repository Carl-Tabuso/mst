import DataTableColumnHeader from '@/components/tasks/components/DataTableColumnHeader.vue'
import DataTableRowActions from '@/components/tasks/components/DataTableRowActions.vue'
import { Badge } from '@/components/ui/badge'
import { User } from '@/types/user'
import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'

export const columns: ColumnDef<User>[] = [
  {
    accessorKey: 'employee.full_name',
    header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Name' }),
    cell: ({ row }) => {
      const name = row.original.employee?.full_name || 'N/A'
      return h('div', { class: 'font-medium' }, name)
    },
  },
  {
    accessorKey: 'email',
    header: ({ column }) =>
      h(DataTableColumnHeader, { column, title: 'Email' }),
  },
  {
    accessorKey: 'created_at',
    header: ({ column }) =>
      h(DataTableColumnHeader, { column, title: 'Created At' }),
    cell: ({ row }) => {
      const date = row.original.created_at
        ? new Date(row.original.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          })
        : 'N/A'
      return h('div', date)
    },
  },
  {
    id: 'status',
    header: ({ column }) =>
      h(DataTableColumnHeader, { column, title: 'Status' }),
    cell: ({ row }) => {
      // Determine status based on deleted_at
      const status = row.original.deleted_at ? 'inactive' : 'active'

      return h(
        Badge,
        {
          variant: 'outline',
          class: [
            status === 'active'
              ? 'bg-green-100 text-green-800 border-green-200'
              : '',
            status === 'inactive'
              ? 'bg-red-100 text-red-800 border-red-200'
              : '',
          ],
        },
        status.charAt(0).toUpperCase() + status.slice(1),
      )
    },
    filterFn: (row, id, value) => {
      const status = row.original.deleted_at ? 'inactive' : 'active'
      return value.includes(status)
    },
  },
  {
    id: 'actions',
    cell: ({ row }) => h(DataTableRowActions, { row }),
  },
]
