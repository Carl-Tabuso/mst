import type { ColumnDef } from '@tanstack/vue-table'
import type { Task } from '../data/schema'

import { h } from 'vue'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { labels, priorities, statuses } from '../data/data'
import DataTableColumnHeader from './DataTableColumnHeader.vue'
import DataTableRowActions from './DataTableRowActions.vue'

export const columns: ColumnDef<Task>[] = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
      'class': 'translate-y-0.5',
    }),
    cell: ({ row }) => h(Checkbox, { 'modelValue': row.getIsSelected(), 'onUpdate:modelValue': value => row.toggleSelected(!!value), 'ariaLabel': 'Select row', 'class': 'translate-y-0.5' }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'frontliner',
    header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Frontliner' }),
    cell: ({ row }) => h('div', { class: 'w-20' }, row.getValue('frontliner')),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'type',
    header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Type of Service' }),

    cell: ({ row }) => {
      const label = labels.find(label => label.value === row.original.label)

      return h('div', { class: 'flex space-x-2' }, [
        label ? h(Badge, { variant: 'outline' }, () => label.label) : null,
        h('span', { class: 'max-w-[500px] truncate font-medium' }, row.getValue('type')),
      ])
    },
  },
  {
    accessorKey: 'date',
    header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Date & Time Requested' }),
    cell: ({ row }) => h('div', { class: 'w-20' }, row.getValue('date')),
    enableSorting: false,
    enableHiding: false,
  },
    {
    accessorKey: 'reason',
    header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Reason' }),
    cell: ({ row }) => h('div', { class: 'w-80' }, row.getValue('reason')),
    enableSorting: false,
    enableHiding: false,
  },
{
  accessorKey: 'status',
  header: ({ column }) => h(DataTableColumnHeader, { column, title: 'Status' }),

  cell: ({ row }) => {
    const status = statuses.find(
      status => status.value === row.getValue('status'),
    )

    if (!status)
      return null

    const statusStyles = {
      'approved': 'inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/20 ring-inset',
      'rejected': 'inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/10 ring-inset',
      'pending': 'inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-yellow-600/20 ring-inset'
    };

    const statusValue = row.getValue('status');
    const classes = statusStyles[statusValue] || statusStyles['pending'];

    return h('div', { class: classes }, [
      h('span', status.label),
    ])
  },
  filterFn: (row, id, value) => {
    return value.includes(row.getValue(id))
  },
},

  {
    id: 'actions',
    cell: ({ row }) => h(DataTableRowActions, { row }),
  },
]
