import { JobOrder } from '@/types'
import { ColumnDef, RowData } from '@tanstack/vue-table'
import { Checkbox } from '@/components/ui/checkbox'
import { h } from 'vue'
import { Badge } from '@/components/ui/badge'
import DropdownAction from './DataTableDropdown.vue'
import '@tanstack/vue-table'
import DataTableHeader from './DataTableHeader.vue'
import { MonitorCog, Truck, Wrench } from 'lucide-vue-next'

declare module '@tanstack/vue-table' {
    interface ColumnMeta<TData extends RowData, TValue> {
        label?: string
    }
}

type BadgeType = keyof typeof badgeMap
const badgeMap = {
    'for viewing': 'continuous',
    'for approval': 'continuous',
    'for proposal': 'continuous',
    'for verification': 'continuous',
    'for appraisal': 'continuous',
    'for personnel assignment': 'continuous',
    'for safety inspection': 'continuous',
    'on-hold': 'continuous',
    'failed': 'destructive',
    'closed': 'secondary',
    'dropped': 'destructive',
    'successful': 'success',
    'completed': 'success',
    'hauling in-progress': 'progress',
    'form4': 'wms',
    'form5': 'secondary',
    'it_service': 'its',
    default: 'default'
} as const;

type IconType = keyof typeof iconMap
const iconMap = {
    'form4': Truck,
    'form5': Wrench,
    'it_service': MonitorCog,
}

type ServiceType = keyof typeof serviceTypeMap
const serviceTypeMap = {
    'it_service': 'IT Services',
    'form4': 'Waste Management',
    'form5': 'Other Services',
}

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
        cell: ({ row }) => h('div', { class: 'text-[13px] font-medium truncate' }, row.getValue('client'))
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
                h(icon, { size: 20, strokeWidth: .75, class: 'mr-2.5' }),
                h('span', { class: 'text-[13px] font-medium truncate' }, formatted)
            ])
        },
    },
    {
        accessorKey: 'contactNo',
        meta: { label: 'Contact Person' },
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
            const variant = badgeMap[status as BadgeType]
            const formatted = () => status
                .split(" ")
                .map((word) => { 
                    return word.charAt(0).toUpperCase() + word.slice(1) 
                }).join(" ")

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
    {
        id: 'actions',
        cell: ({ row }) => {
            const jobOrder = row.original

            return h('div', { class: 'relative' }, h(DropdownAction, {
                jobOrder,
            }))
        },
        enableHiding: false,
    }
]