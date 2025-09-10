import CreatorAndTimestamp from '@/components/CreatorAndTimestamp.vue'
import { Badge } from '@/components/ui/badge'
import { Checkbox } from '@/components/ui/checkbox'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { JobOrder } from '@/types'
import { ColumnDef, RowData } from '@tanstack/vue-table'
import { format } from 'date-fns'
import { MonitorCog, Truck, Wrench } from 'lucide-vue-next'
import { h } from 'vue'
import DataTableHeader from './DataTableHeader.vue'
import ForceDeleteJobOrder from './ForceDeleteJobOrder.vue'
import RestoreJobOrder from './RestoreJobOrder.vue'

declare module '@tanstack/vue-table' {
  interface ColumnMeta<TData extends RowData, TValue> {
    label?: string
  }
}

type IconType = keyof typeof iconMap
const iconMap = {
  form4: Truck,
  form5: Wrench,
  it_service: MonitorCog,
}

type ServiceType = keyof typeof serviceTypeMap
const serviceTypeMap = {
  it_service: 'IT Services',
  form4: 'Waste Management',
  form5: 'Other Services',
}

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
        'div',
        {
          class:
            'text-muted-foreground text-[13px] font-medium truncate tracking-tighter',
        },
        row.getValue('ticket'),
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
    accessorKey: 'serviceableType',
    meta: { label: 'Type of Service' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const serviceType = row.getValue('serviceableType')
      const formatted = serviceTypeMap[serviceType as ServiceType]
      const icon = iconMap[serviceType as IconType]

      return h('div', { class: 'flex items-center' }, [
        h(icon, { size: 20, strokeWidth: 0.75, class: 'mr-2' }),
        h('span', { class: 'text-[13px] font-medium' }, formatted),
      ])
    },
  },
  {
    accessorKey: 'status',
    meta: { label: 'Status' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const status =
        useJobOrderDicts().statusMap[row.getValue('status') as string]
      return h(
        'div',
        h(
          Badge,
          { variant: status.badge, class: 'truncate' },
          () => status.label,
        ),
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
      const formattedDate = format(dateTime, 'MMMM dd, yyyy')
      const formattedTime = format(dateTime, 'hh:mm a')

      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-medium' }, formattedDate),
        h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime),
      ])
    },
  },
  {
    accessorKey: 'archivedAt',
    meta: { label: 'Date Archived' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const archivedAt = new Date(row.getValue('archivedAt'))
      const formattedDate = format(archivedAt, 'MMMM dd, yyyy')
      const formattedTime = format(archivedAt, 'hh:mm a')

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
        h(RestoreJobOrder, { jobOrder: row.original }),
        h(ForceDeleteJobOrder, { jobOrder: row.original }),
      ])
    },
    enableHiding: false,
  },
]
