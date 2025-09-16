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
import { router, usePage } from '@inertiajs/vue3'
import type {
  ColumnDef,
  PaginationState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { computed, ref } from 'vue'
import UserDataTableToolbar from './DataTableToolbar.vue'

interface DataTableProps {
  columns: ColumnDef<TData, TValue>[]
  data: TData[] | { data: TData[]; meta: EloquentCollection }
  meta?: EloquentCollection
  emptyImgUri: string
  routeName?: string
}

const props = withDefaults(defineProps<DataTableProps>(), {
  routeName: 'users.index',
})

const page = usePage()

const tableData = computed(() => {
  return Array.isArray(props.data) ? props.data : props.data.data
})

const tableMeta = computed(() => {
  return props.meta || (Array.isArray(props.data) ? undefined : props.data.meta)
})

const sorting = ref<SortingState>()
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const pagination = ref<PaginationState>({
  pageIndex: tableMeta.value ? tableMeta.value.current_page - 1 : 0,
  pageSize: tableMeta.value ? tableMeta.value.per_page : 10,
})
const globalFilter = ref<string>(page.props.filters?.search || '')

const getFilters = () => ({
  ...(page.props.filters || {}),
  search: globalFilter.value,
  per_page: pagination.value.pageSize,
})

const table = useVueTable({
  get data() {
    return tableData.value
  },
  get columns() {
    return props.columns
  },
  manualPagination: true,
  manualFiltering: true,
  rowCount: tableMeta.value ? tableMeta.value.total : tableData.value.length,
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
      route(props.routeName),
      { filters: getFilters() },
      { preserveState: true, preserveScroll: true, replace: true },
    )
  },
  onPaginationChange: (updater) => {
    valueUpdater(updater, pagination)
    const { pageIndex } = pagination.value
    router.get(
      route(props.routeName),
      { page: pageIndex + 1, filters: getFilters() },
      { preserveState: true, preserveScroll: true, replace: true },
    )
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
    get globalFilter() {
      return globalFilter.value
    },
  },
})
</script>

<template>
  <UserDataTableToolbar
    :table="table"
    :globalFilter="globalFilter"
    :routeName="routeName"
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
          v-if="table.getRowModel()?.rows?.length"
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
          <TableRow class="h-[40dvh] hover:bg-transparent">
            <TableCell
              :colspan="columns.length"
              class="text-center align-middle"
            >
              <div
                class="my-10 flex h-full flex-col items-center justify-center gap-8"
              >
                <img
                  v-if="emptyImgUri"
                  :src="emptyImgUri"
                  alt="No results found"
                  class="h-[130px] w-[130px]"
                />
                <div class="flex w-[290px] flex-col gap-2">
                  <div class="text-3xl font-extrabold text-primary">
                    No results found
                  </div>
                  <div class="">
                    Couldn't find
                    <span class="font-semibold">"{{ globalFilter }}".</span>
                    Please ensure that you have entered the correct keyword.
                  </div>
                </div>
              </div>
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
  <DataTablePagination
    v-if="tableMeta"
    :table="table"
    :last-page="tableMeta.last_page"
  />
</template>