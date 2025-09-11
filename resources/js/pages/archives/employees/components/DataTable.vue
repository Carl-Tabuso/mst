<script setup lang="ts" generic="TValue">
import DataTablePagination from '@/components/DataTablePagination.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { valueUpdater } from '@/lib/utils'
import { EloquentCollection, Employee } from '@/types'
import type { ColumnDef } from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { useArchivedEmployeeTable } from '../../composables/useArchivedEmployeeTable'
import DataTableToolbar from './DataTableToolbar.vue'

interface DataTableProps<TData> {
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  meta: EloquentCollection
}

const props = defineProps<DataTableProps<Employee>>()

const { dataTable } = useArchivedEmployeeTable()

const table = useVueTable({
  get data() {
    return props.data
  },
  get columns() {
    return props.columns
  },
  manualPagination: true,
  manualFiltering: true,
  rowCount: props.meta.total,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: (updater) => valueUpdater(updater, dataTable.sorting),
  onColumnVisibilityChange: (updater) =>
    valueUpdater(updater, dataTable.columnVisibility),
  onRowSelectionChange: (updater) =>
    valueUpdater(updater, dataTable.rowSelection),
  onGlobalFilterChange: (updater) => {
    valueUpdater(updater, dataTable.globalFilter)
    table.setPageIndex(0)
    // applyFilters()
  },
  onPaginationChange: (updater) => {
    valueUpdater(updater, dataTable.pagination)
    // router.get(
    //   route('archive.job_order.index'),
    //   dataTableStateRequestPayload.value,
    //   {
    //     preserveState: true,
    //     preserveScroll: true,
    //     replace: true,
    //   },
    // )
  },
  state: {
    get sorting() {
      return dataTable.sorting.value
    },
    get columnVisibility() {
      return dataTable.columnVisibility.value
    },
    get rowSelection() {
      return dataTable.rowSelection.value
    },
    get pagination() {
      return dataTable.pagination.value
    },
    get globalFilter() {
      return dataTable.globalFilter.value
    },
  },
})
</script>

<template>
  <DataTableToolbar
    :table="table"
    :globalFilter="dataTable.globalFilter.value"
  />
  <div class="rounded-md border">
    <Table>
      <TableHeader>
        <TableRow
          v-for="headerGroup in table.getHeaderGroups()"
          :key="headerGroup.id"
          class=""
        >
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
          >
            <FlexRender
              v-if="!header.isPlaceholder"
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template
          v-if="table.getRowModel().rows?.length"
          v-for="row in table.getRowModel().rows"
          :key="row.id"
        >
          <TableRow :data-state="row.getIsSelected() ? 'selected' : undefined">
            <TableCell
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              class="py-3"
            >
              <FlexRender
                :render="cell.column.columnDef.cell"
                :props="cell.getContext()"
              />
            </TableCell>
          </TableRow>
        </template>
        <template v-else>
          <TableRow>
            <TableCell
              :colspan="columns.length"
              class="h-24 text-center"
            >
              No results found.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
  <DataTablePagination
    :table="table"
    :lastPage="props.meta.last_page"
  />
</template>
