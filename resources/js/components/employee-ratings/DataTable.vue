<script setup lang="ts" generic="TData, TValue">
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { valueUpdater } from '@/lib/utils'
import { EloquentCollection } from '@/types'
import type {
  ColumnDef,
  PaginationState,
  SortingState,
  VisibilityState,
  ColumnFiltersState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ref, computed } from 'vue'
import DataTablePagination from './DataTablePagination.vue'
import DataTableToolbar from './DataTableToolbar.vue'

const props = defineProps<{
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  meta: EloquentCollection
  routeName: string
  filters?: {
    search?: string
    statuses?: string[]
    fromDateOfService?: string
    toDateOfService?: string
  }
}>()

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({
  client: true,
  contactNo: false,
  address: false,
  status: true,
  creator: true,
  dateTime: false,
})
const rowSelection = ref({})

const searchQuery = new URLSearchParams(window.location.search).get('search') || ''
const globalFilter = ref<string | number>(searchQuery)

const pagination = ref<PaginationState>({
  pageIndex: 0,
  pageSize: props.meta?.per_page ?? 10,
})

const table = useVueTable({
  get data() {
    return props.data ?? []
  },
  get columns() {
    return props.columns
  },

  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),

  onSortingChange: (updater) => valueUpdater(updater, sorting),
  onColumnFiltersChange: (updater) => valueUpdater(updater, columnFilters),
  onColumnVisibilityChange: (updater) => valueUpdater(updater, columnVisibility),
  onRowSelectionChange: (updater) => valueUpdater(updater, rowSelection),

  onGlobalFilterChange: (updater) => {
    valueUpdater(updater, globalFilter)
    table.setPageIndex(0)
  },

  onPaginationChange: (updater) => {
    valueUpdater(updater, pagination)
  },

  getRowId: (row) =>
    (row as { id?: string | number; serviceableId?: string | number; ticket?: string }).ticket ??
    (row as { id?: string | number; serviceableId?: string | number }).id ??
    (row as any).serviceableId,

  state: {
    get sorting() {
      return sorting.value
    },
    get columnFilters() {
      return columnFilters.value
    },
    get columnVisibility() {
      return columnVisibility.value
    },
    get rowSelection() {
      return rowSelection.value
    },
    get pagination() {
      return pagination.value
    },
    get globalFilter() {
      return globalFilter.value
    },
  },

  globalFilterFn: (row, value) => {
    const search = String(value).toLowerCase()

    return row.getVisibleCells().some(cell => {
      const cellValue = cell.getValue()
      if (cellValue == null) return false

      const rowData = row.original as any
      if (rowData.ticket && typeof rowData.ticket === 'string') {
        if (rowData.ticket.toLowerCase().includes(search)) {
          return true
        }
      }

      if (rowData.id && String(rowData.id).includes(search)) {
        return true
      }

      if (rowData.rating_status &&
        String(rowData.rating_status).toLowerCase().includes(search)) {
        return true
      }

      if (typeof cellValue === 'string') {
        return cellValue.toLowerCase().includes(search)
      }

      if (typeof cellValue === 'number') {
        return String(cellValue).includes(search)
      }

      if (typeof cellValue === 'object') {
        return JSON.stringify(cellValue).toLowerCase().includes(search)
      }

      return String(cellValue).toLowerCase().includes(search)
    })
  },
})

const filteredRowCount = computed(() => table.getFilteredRowModel().rows.length)
const filteredMeta = computed(() => ({
  ...props.meta,
  total: filteredRowCount.value,
  current_page: pagination.value.pageIndex + 1,
  per_page: pagination.value.pageSize,
  last_page: Math.ceil(filteredRowCount.value / pagination.value.pageSize) || 1,
}))
</script>

<template>
  <DataTableToolbar :table="table" :globalFilter="globalFilter" :routeName="props.routeName" :filters="props.filters" />

  <div class="rounded-md border mt-4">
    <Table>
      <TableHeader>
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id" class="">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
              :props="header.getContext()" />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel().rows?.length">
          <template v-for="row in table.getRowModel().rows" :key="row.id">
            <TableRow :data-state="row.getIsSelected() ? 'selected' : undefined">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="py-3">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
              </TableCell>
            </TableRow>
          </template>
        </template>
        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              <div v-if="globalFilter">
                No results found for "{{ globalFilter }}"
              </div>
              <div v-else>
                No results.
              </div>
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>

  <DataTablePagination v-if="filteredMeta" :table="table" :lastPage="filteredMeta.last_page" :meta="filteredMeta" />
</template>