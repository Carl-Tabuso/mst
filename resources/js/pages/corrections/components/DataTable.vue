<script setup lang="ts" generic="TData, TValue">
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
import { EloquentCollection } from '@/types'
import { router } from '@inertiajs/vue3'
import {
  ColumnDef,
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  SortingState,
  useVueTable,
  VisibilityState,
} from '@tanstack/vue-table'
import { useUrlSearchParams } from '@vueuse/core'
import { ref } from 'vue'
import DataTableToolbar from './DataTableToolbar.vue'

interface DataTableProps {
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  meta: EloquentCollection
}

const props = defineProps<DataTableProps>()

const sorting = ref<SortingState>([])
const columnVisibility = ref<VisibilityState>({
  reason: false,
})
const rowSelection = ref({})
const pagination = ref<PaginationState>({
  pageIndex: props.meta.current_page - 1,
  pageSize: props.meta.per_page,
})
const urlParams = useUrlSearchParams('history')
const globalFilter = ref<string>(String(urlParams.search || ''))

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
  onSortingChange: (updater) => valueUpdater(updater, sorting),
  onColumnVisibilityChange: (updater) =>
    valueUpdater(updater, columnVisibility),
  onRowSelectionChange: (updater) => valueUpdater(updater, rowSelection),
  onGlobalFilterChange: (updater) => {
    valueUpdater(updater, globalFilter)
    table.setPageIndex(0)
    router.get(
      route('job_order.correction.index'),
      {
        search: globalFilter.value,
      },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    )
  },
  onPaginationChange: (updater) => {
    valueUpdater(updater, pagination)
    const { pageIndex, pageSize } = pagination.value
    const data = {
      page: pageIndex + 1,
      per_page: pageSize,
      ...(globalFilter.value ? { search: globalFilter.value } : {}),
    }
    router.get(route('job_order.correction.index'), data, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  },
  state: {
    get sorting() {
      return sorting.value
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
  },
})
</script>

<template>
  <DataTableToolbar
    :table="table"
    :global-filter="globalFilter"
  />
  <div class="rounded-md border">
    <Table>
      <TableHeader>
        <TableRow
          v-for="headerGroup in table.getHeaderGroups()"
          :key="headerGroup.id"
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
        <template v-else> None. </template>
      </TableBody>
    </Table>
  </div>
  <DataTablePagination
    :table="table"
    :last-page="meta.last_page"
  />
</template>
