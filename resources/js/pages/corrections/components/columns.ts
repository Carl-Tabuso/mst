import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import DataTableHeader from '@/pages/corrections/components/DataTableHeader.vue'
import { JobOrderCorrection } from '@/types'
import { Link } from '@inertiajs/vue3'
import { ColumnDef } from '@tanstack/vue-table'
import { format } from 'date-fns'
import { h } from 'vue'
import ArchiveRow from './ArchiveRow.vue'
import RequestingFrontliner from './RequestingFrontliner.vue'

const { correctionStatusMap } = useJobOrderDicts()

export const columns: ColumnDef<JobOrderCorrection>[] = [
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
    accessorKey: 'id',
    meta: { label: 'Ticket' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      return h(
        Link,
        {
          href: route('job_order.correction.show', row.getValue('id')),
          class:
            'text-primary underline hover:opacity-80 text-[13px] font-medium truncate tracking-tighter',
        },
        () => row.original.jobOrder.ticket,
      )
    },
    enableHiding: false,
  },
  {
    accessorKey: 'reason',
    meta: { label: 'Reason' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      return h(
        'div',
        { class: 'text-xs font-medium truncate w-56' },
        row.getValue('reason'),
      )
    },
  },
  {
    accessorKey: 'creator',
    meta: { label: 'Requesting Frontliner' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h(RequestingFrontliner, { row: row }),
  },
  {
    accessorKey: 'status',
    meta: { label: 'Request Status' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const status: string = row.getValue('status')
      const mappedStatus = correctionStatusMap[status]

      return h(Badge, { variant: mappedStatus.badge }, () => mappedStatus.label)
    },
  },
  // {
  //   id: 'errorCount',
  //   meta: { label: 'Errors' },
  //   header: ({ column }) => h(DataTableHeader, { column: column }),
  //   cell: ({ row }) => {
  //     return h('div', { class: 'font-semibold text-destructive' }, row.original.jobOrder.errorCount)
  //   }
  // },
  {
    accessorKey: 'createdAt',
    meta: { label: 'Date Requested' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateCreated = new Date(row.getValue('createdAt'))
      const formattedDate = format(dateCreated, 'MMMM dd, yyyy (EEEE)')
      const formattedTime = format(dateCreated, 'h:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    id: 'archive',
    cell: ({ row }) => h(ArchiveRow, { correction: row.original }),
    enableHiding: false,
  },
]
