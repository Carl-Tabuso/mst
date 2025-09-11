import CreatorAndTimestamp from '@/components/CreatorAndTimestamp.vue'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import ArchiveColumn from '@/pages/job-orders/components/ArchiveJobOrder.vue'
import DataTableHeader from '@/pages/job-orders/components/DataTableHeader.vue'
import { JobOrder } from '@/types'
import { Link } from '@inertiajs/vue3'
import { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'

export const columns: ColumnDef<JobOrder>[] = [
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
    accessorKey: 'ticket',
    meta: { label: 'Ticket' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      return h(
        Link,
        {
          href: route('job_order.other_services.edit', row.getValue('ticket')),
          class:
            'text-primary underline hover:opacity-80 text-[13px] font-medium truncate tracking-tighter',
        },
        () => row.getValue('ticket'),
      )
    },
    enableHiding: false,
  },
  {
    accessorKey: 'client',
    meta: { label: 'Client' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) =>
      h(
        'div',
        { class: 'text-[13px] font-medium truncate' },
        row.getValue('client'),
      ),
  },
  {
    accessorKey: 'contactNo',
    meta: { label: 'Contact Person' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const contactNo = String(row.getValue('contactNo'))
      const contactPerson = row.original.contactPerson

      return h('div', [
        h('div', { class: 'text-xs font-semibold' }, contactPerson),
        h('div', { class: 'text-[11px] text-muted-foreground' }, contactNo),
      ])
    },
  },
  {
    accessorKey: 'address',
    meta: { label: 'Address' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h('div', { class: 'text-xs' }, row.getValue('address')),
  },
  {
    accessorKey: 'status',
    meta: { label: 'Status' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const status: string = row.getValue('status')
      const variant = JobOrderStatuses.find((j) => j.id === status)?.badge
      const formatted = () =>
        status
          .split(' ')
          .map((word) => {
            return word.charAt(0).toUpperCase() + word.slice(1)
          })
          .join(' ')

      return h(
        'div',
        h(Badge, { variant: variant, class: 'truncate' }, formatted),
      )
    },
  },
  {
    accessorKey: 'creator',
    meta: { label: 'Frontliner' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h(CreatorAndTimestamp, { row: row }),
  },
  {
    accessorKey: 'dateTime',
    meta: { label: 'Date & Time of Service' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateTime = new Date(row.getValue('dateTime'))
      const formattedDate = dateTime.toLocaleString('en-ph', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
      const formattedTime = dateTime.toLocaleTimeString('en-ph', {
        hour: '2-digit',
        minute: '2-digit',
      })

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-semibold' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    id: 'archive',
    cell: ({ row }) => h(ArchiveColumn, { jobOrder: row.original }),
    enableHiding: false,
  },
]
