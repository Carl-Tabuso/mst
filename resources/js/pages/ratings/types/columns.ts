import { JobOrder } from '@/types'
import { ColumnDef, RowData } from '@tanstack/vue-table'
import { h } from 'vue'
import { router } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import DataTableHeader from '@/components/employee-ratings/DataTableHeader.vue'
import '@tanstack/vue-table'

declare module '@tanstack/vue-table' {
  interface ColumnMeta<TData extends RowData, TValue> {
    label?: string
  }
}

const badgeMap = {
  'to be evaluated': 'warning',
  'evaluation done': 'success',
  default: 'default',
} as const

export const columns: ColumnDef<JobOrder>[] = [
  {
    accessorKey: 'id',
    meta: { label: 'Job Order' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => h('div', { class: 'text-sm font-medium' }, `JO #${row.getValue('id')}`),
  },
  {
    accessorKey: 'date_time',
    meta: { label: 'Date & Time of Service' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const rawDate = row.getValue('date_time');
      if (!rawDate) return h('div', '—');
  
      const dateTime = new Date(rawDate);
      return h('div', { class: 'text-xs' }, [
        h('div', { class: 'font-semibold' }, dateTime.toLocaleDateString('en-PH', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        })),
        h('div', { class: 'text-[11px] text-muted-foreground' }, dateTime.toLocaleTimeString('en-PH', {
          hour: '2-digit',
          minute: '2-digit',
        })),
      ]);
    },
  },  
  {
    accessorKey: 'assignedPersonnel',
    meta: { label: 'Assigned Personnel' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const names = row.original.assignedPersonnel || []
      return h('div', { class: 'text-xs whitespace-pre-line' }, names.join('\n'))
    },
  },
  {
    accessorKey: 'rating_status',
    meta: { label: 'Status' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const status: string = row.getValue('rating_status') ?? '';
      const variant = status === 'Evaluation Done' ? 'success' : 'warning';
      return h(Badge, { variant, class: 'truncate' }, () => status);
    },
  },
  {
    accessorKey: 'Action',
    meta: { label: 'Action' },
    header: ({ column }) => h(DataTableHeader, { column }),
    id: 'actions',
    cell: ({ row }) => {
        const status = row.original.rating_status ?? ''
        const jobOrderId = row.original.id

        const isEvaluated = status === 'Evaluation Done'
        const text = isEvaluated ? 'View Evaluation' : 'Evaluate Now'
        const routeName = isEvaluated ? 'employee.ratings.view' : 'employee.ratings.create'

        return h(
            'button',
            {
                onClick: () => {
                    router.get(route(routeName), { job_order_id: jobOrderId })
                },
                class: [
                    'text-sm font-medium',
                    isEvaluated
                        ? 'text-zinc-500 underline hover:text-zinc-700'
                        : 'text-lime-600 underline hover:text-lime-800'
                ]
            },
            text
        )
    },
    enableHiding: false
}

]
