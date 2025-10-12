import { Checkbox } from '@/components/ui/checkbox'
import { Truck } from '@/types'
import { ColumnDef } from '@tanstack/vue-table'
import { format } from 'date-fns'
import { h } from 'vue'
import AddedBy from './AddedBy.vue'
import DataTableHeader from './DataTableHeader.vue'
import ForceDeleteTruck from './ForceDeleteTruck.vue'
import RestoreTruck from './RestoreTruck.vue'

export const columns: ColumnDef<Truck>[] = [
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
    accessorKey: 'model',
    meta: { label: 'Model' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) =>
      h('span', { class: 'text-[13px]' }, row.getValue('model')),
  },
  {
    accessorKey: 'plateNo',
    meta: { label: 'Plate Number' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) =>
      h('span', { class: 'text-[13px]' }, row.getValue('plateNo')),
    enableHiding: false,
  },
  {
    accessorKey: 'creator',
    meta: { label: 'Added By' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => h(AddedBy, { dispatcher: row.original.creator }),
  },
  {
    accessorKey: 'archivedAt',
    meta: { label: 'Date Archived' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateArchived = new Date(row.getValue('archivedAt'))
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
        h(RestoreTruck, { truck: row.original }),
        h(ForceDeleteTruck, { truck: row.original }),
      ])
    },
    enableHiding: false,
  },
]
