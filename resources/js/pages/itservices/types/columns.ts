import { JobOrder } from '@/types'
import { ColumnDef, RowData } from '@tanstack/vue-table'
import { Checkbox } from '@/components/ui/checkbox'
import { h } from 'vue'
import { Badge } from '@/components/ui/badge'
import DropdownAction from '@/components/main-job-orders/DataTableDropdown.vue'
import DataTableHeader from '@/components/main-job-orders/DataTableHeader.vue'
import '@tanstack/vue-table'

declare module '@tanstack/vue-table' {
    interface ColumnMeta<TData extends RowData, TValue> {
        label?: string
    }
}

const badgeMap = {
    'for verification': 'continuous',
    'for personnel assignment': 'continuous',
    'for safety inspection': 'continuous',
    'on-hold': 'continuous',
    'closed': 'secondary',
    'completed': 'success',
    'dropped': 'destructive',
    default: 'default'
} as const;

export const columns: ColumnDef<JobOrder>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            'checked': table.getIsAllPageRowsSelected(),
            'onUpdate:checked': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
            'ariaLabel': 'Select all',
            class: 'border-gray-400 dark:border-white'
        }),
        cell: ({ row }) => h(Checkbox, {
            'checked': row.getIsSelected(),
            'onUpdate:checked': (value: boolean) => row.toggleSelected(!!value),
            'ariaLabel': 'Select row',
            class: 'border-gray-400 dark:border-white'
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'client',
        meta: { label: 'Client' },
        header: ({ column }) => h(DataTableHeader, { column: column }),
        cell: ({ row }) => h('div', { class: 'text-[13px] font-medium truncate' }, row.getValue('client')),
        enableHiding: false,
    },
    {
        accessorKey: 'contactNo',
        meta: { label: 'Contact No' },
        header: ({ column }) => h(DataTableHeader, { column: column }),
        cell: ({ row }) => {
            const contactNo = String(row.getValue('contactNo'))
            const contactPerson = row.original.contactPerson
            return h('div', [
                h('div', { class: 'text-xs font-semibold'}, contactPerson),
                h('div', { class: 'text-[11px] text-muted-foreground'}, contactNo)
            ])
        }
    },
    {
        accessorKey: 'address',
        meta: { label: 'Address' },
        header: ({ column }) => h(DataTableHeader, { column: column }),
        cell: ({ row }) => h('div', { class: 'text-xs' }, row.getValue('address'))
    },
    {
        accessorKey: 'status',
        meta: { label: 'Status' },
        header: ({ column }) => h(DataTableHeader, { column: column }),
        cell: ({ row }) => {
            const status: string = row.getValue('status')
            const variant = badgeMap[status as keyof typeof badgeMap] ?? badgeMap.default
            const formatted = () => status
                .split(" ")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" ")
            return h('div', h(Badge, { variant: variant, class: 'truncate' }, formatted))
        }
    },
    {
        accessorKey: 'creator',
        meta: { label: 'Frontliner' },
        header: ({ column }) => h(DataTableHeader, { column: column }),
        cell: ({ row }) => {
            const dateTime = new Date(row.original.createdAt)
            const formattedDateTime = dateTime.toLocaleString('en-ph', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
            return h('div', [
                h('div', { class: 'text-xs font-semibold' }, row.original.creator?.fullName),
                h('div', { class: 'text-[11px] text-muted-foreground'}, `on ${formattedDateTime}`)
            ])
        }
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
                h('div', { class: 'text-[11px] text-muted-foreground' }, formattedTime)
            ])
        },
    },
    // {
    //     accessorKey: 'machineInfos',
    //     meta: { label: 'Machines' },
    //     header: ({ column }) => h(DataTableHeader, { column: column }),
    //     cell: ({ row }) => {
    //         const machines = row.original.machineInfos || []
    //         if (!machines.length) return h('span', { class: 'text-xs text-muted-foreground' }, '—')
    //         return h('ul', { class: 'text-xs list-disc ml-4' }, machines.map((m: any) =>
    //             h('li', {}, `${m.machine_type} (${m.model})`)
    //         ))
    //     }
    // },
    {
        id: 'actions',
        cell: ({ row }) => {
            const jobOrder = row.original
            return h('div', { class: 'relative' }, h(DropdownAction, { jobOrder }))
        },
        enableHiding: false,
    }
    //add more if needed(wait for the ligma)
]