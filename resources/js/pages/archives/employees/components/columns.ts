import { Checkbox } from '@/components/ui/checkbox'
import { Employee, Position } from '@/types'
import { ColumnDef } from '@tanstack/vue-table'
import { format } from 'date-fns'
import { h } from 'vue'
import DataTableHeader from './DataTableHeader.vue'
import EmployeeNameAndPosition from './EmployeeNameAndPosition.vue'
import ForceDeleteEmployee from './ForceDeleteEmployee.vue'
import RestoreEmployee from './RestoreEmployee.vue'

export const columns: ColumnDef<Employee>[] = [
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
    accessorKey: 'fullName',
    meta: { label: 'Full Name' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) =>
      h(EmployeeNameAndPosition, {
        employee: row.original as Employee & { position: Position },
      }),
    enableHiding: false,
  },
  {
    accessorKey: 'dateOfBirth',
    meta: { label: 'Date of Birth' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dob = new Date(row.getValue('dateOfBirth'))
      return h('span', { class: 'text-[13px]' }, format(dob, 'MMMM dd, yyyy'))
    },
  },
  {
    accessorKey: 'contactNumber',
    meta: { label: 'Contact Number' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      return h('span', { class: 'text-[13px]' }, row.getValue('contactNumber'))
    },
  },
  {
    accessorKey: 'createdAt',
    meta: { label: 'Date Created' },
    header: ({ column }) => h(DataTableHeader, { column: column }),
    cell: ({ row }) => {
      const dateCreated = new Date(row.getValue('createdAt'))
      const formattedDate = format(dateCreated, 'MMMM dd, yyyy')
      const formattedTime = format(dateCreated, 'hh:mm a')

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
        h(RestoreEmployee, { employee: row.original }),
        h(ForceDeleteEmployee, { employee: row.original }),
      ])
    },
    enableHiding: false,
  },
]
