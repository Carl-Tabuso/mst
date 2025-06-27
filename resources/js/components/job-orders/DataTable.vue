<script setup lang="ts" generic="TData, TValue">
import type { 
  ColumnDef,
  SortingState,
  VisibilityState,
  PaginationState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { ref } from 'vue'
import { valueUpdater } from '@/lib/utils'
import { router } from '@inertiajs/vue3'
import { EloquentCollection } from '@/types'
import DataTablePagination from './DataTablePagination.vue'
import DataTableToolbar from './DataTableToolbar.vue'

const props = defineProps<{
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  meta: EloquentCollection
}>()

const sorting = ref<SortingState>([])
const columnVisibility = ref<VisibilityState>({
  client: true,
  contactNo: false,
  address: false,
  status: true,
  creator: true,
  dateTime: false,
})
const rowSelection = ref({})
const pagination = ref<PaginationState>({
  pageIndex: props.meta.current_page - 1,
  pageSize: props.meta.per_page
})
const searchQuery = new URLSearchParams(window.location.search).get('search') || ''
const globalFilter = ref<string | number>(searchQuery)

const table = useVueTable({
  get data() { return props.data },
  get columns() { return props.columns },
  manualPagination: true,
  manualFiltering: true,
  rowCount: props.meta.total,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: updater => valueUpdater(updater, sorting),
  onColumnVisibilityChange: updater => valueUpdater(updater, columnVisibility),
  onRowSelectionChange: updater => valueUpdater(updater, rowSelection),
  onGlobalFilterChange: updater => {
    valueUpdater(updater, globalFilter)

    table.setPageIndex(0)

    router.get(route('job_order.index'), {
      search: globalFilter.value,
    }, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  },
  onPaginationChange: updater => {
    valueUpdater(updater, pagination)

    const { pageIndex, pageSize } = pagination.value

    const data = {
      page: pageIndex + 1,
      per_page: pageSize,
      ...(globalFilter.value ? { search: globalFilter.value }: {})
    }
  
    router.get(route('job_order.index'), data, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  },
  state: {
    get sorting() { return sorting.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get pagination() { return pagination.value },
    get globalFilter() { return globalFilter.value },
  }
})
</script>

<template>
  <DataTableToolbar
    :table="table"
    :globalFilter="globalFilter"/>
  <div class="border rounded-md">
    <Table>
      <TableHeader>
        <TableRow 
          v-for="headerGroup in table.getHeaderGroups()" 
          :key="headerGroup.id" 
          class="">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender
              v-if="! header.isPlaceholder" :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
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
              No results.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
  <DataTablePagination
    :table="table"
    :lastPage="props.meta.last_page"/>
</template>
