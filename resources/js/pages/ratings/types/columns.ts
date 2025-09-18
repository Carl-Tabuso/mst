import DataTableHeader from '@/components/employee-ratings/DataTableHeader.vue'
import { Badge } from '@/components/ui/badge'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import '@tanstack/vue-table'
import { ColumnDef, RowData } from '@tanstack/vue-table'
import { h } from 'vue'

declare module '@tanstack/vue-table' {
  interface ColumnMeta<TData extends RowData, TValue> {
    label?: string
  }
}

export const columns: ColumnDef<JobOrder>[] = [
  {
    accessorKey: 'ticket',
    meta: { label: 'Job Order' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const ticket = row.getValue('ticket')
      return h('div', { class: 'text-sm font-medium' }, [
        ticket || `JO #${row.original.id}`,
      ])
    },
  },
  {
    accessorKey: 'date_time',
    meta: { label: 'Date & Time of Service' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const rawDate = row.getValue('date_time')
      if (
        !rawDate ||
        (typeof rawDate !== 'string' &&
          typeof rawDate !== 'number' &&
          !(rawDate instanceof Date))
      ) {
        return h('div', '—')
      }

      const dateTime = new Date(rawDate as string | number | Date)
      return h('div', { class: 'text-xs' }, [
        h(
          'div',
          { class: 'font-semibold' },
          dateTime.toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          }),
        ),
        h(
          'div',
          { class: 'text-[11px] text-muted-foreground' },
          dateTime.toLocaleTimeString('en-PH', {
            hour: '2-digit',
            minute: '2-digit',
          }),
        ),
      ])
    },
  },
  {
    accessorKey: 'rating_status',
    meta: { label: 'Status' },
    header: ({ column }) => h(DataTableHeader, { column }),
    cell: ({ row }) => {
      const status: string = row.getValue('rating_status') ?? ''
      const variant = status === 'Evaluation Done' ? 'success' : 'default'
      return h(Badge, { variant, class: 'truncate' }, () => status)
    },
  },
  {
    accessorKey: 'Action',
    meta: { label: 'Action' },
    header: ({ column }) => h(DataTableHeader, { column }),
    id: 'actions',
    cell: ({ row }) => {
      const status = row.original.rating_status ?? ''
      const jobOrderTicket = row.original.ticket
      const jobOrderId = row.original.id

      const isEvaluated = status === 'Evaluation Done'
      const text = isEvaluated ? 'View Evaluation' : 'Evaluate Now'
      const routeName = isEvaluated
        ? 'employee.ratings.view'
        : 'employee.ratings.create'

      return h(
        'button',
        {
          onClick: () => {
            const params = jobOrderTicket
              ? { job_order_ticket: jobOrderTicket }
              : { job_order_id: jobOrderId }

            router.get(route(routeName), params)
          },
          class: [
            'text-sm font-medium',
            isEvaluated
              ? 'text-zinc-600 underline hover:text-zinc-700'
              : 'text-lime-600 underline hover:text-lime-800',
          ],
        },
        text,
      )
    },
    enableHiding: false,
  },
]
