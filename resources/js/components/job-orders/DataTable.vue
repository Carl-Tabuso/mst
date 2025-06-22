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
import {
  Pagination, 
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import { 
  DropdownMenu, 
  DropdownMenuCheckboxItem, 
  DropdownMenuContent, 
  DropdownMenuLabel, 
  DropdownMenuSeparator, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
import { 
  Select, 
  SelectContent, 
  SelectItem, 
  SelectTrigger, 
  SelectValue 
} from "@/components/ui/select"
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
} from '@/components/ui/form'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { ref, computed, watch } from 'vue'
import { valueUpdater } from '@/lib/utils'
import { 
  Filter, 
  Settings2, 
  Download, 
  Search,
  Archive,
  ChevronsUpDown,
  Calendar,
} from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { router } from '@inertiajs/vue3'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import AppCalendar from '@/components/AppCalendar.vue'

const props = defineProps<{
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  links: {}
  meta: {
    current_page: number,
    last_page: number,
    per_page: number,
    from: Number,
    to: Number,
    total: number,
  }
}>()

const jobOrderStatuses = computed(() => JobOrderStatuses)

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
const pageSizes = [10, 25, 50, 100]
const pagination = ref<PaginationState>({
  pageIndex: props.meta.current_page,
  pageSize: props.meta.per_page
})
const searchQuery = new URLSearchParams(window.location.search).get('search') || ''
const globalFilter = ref<string|number>(searchQuery)

const table = useVueTable({
  get data() { return props.data },
  get columns() { return props.columns },
  manualPagination: true,
  manualFiltering: true,
  rowCount: props.meta.total,
  // pageCount: props.meta.last_page,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: updater => valueUpdater(updater, sorting),
  onColumnVisibilityChange: updater => valueUpdater(updater, columnVisibility),
  onRowSelectionChange: updater => valueUpdater(updater, rowSelection),
  onGlobalFilterChange: updater => {
    valueUpdater(updater, globalFilter)

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

    router.get(route('job_order.index'), {
      search: globalFilter.value,
      page: pagination.value.pageIndex,
      per_page: pagination.value.pageSize,
    }, {
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

const hasRowSelection = computed(() => table.getSelectedRowModel().rows.length > 0);

const sortBy = ref('asc')
</script>

<template>
  <div class="flex items-center py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text" 
        class="max-w-sm pl-12 h-9"
        placeholder="Search"
        :modelValue="globalFilter"
        @update:modelValue="(value) => table.setGlobalFilter(String(value))" />
      <span class="absolute start-0 inset-y-0 flex items-center justify-center px-5">
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>
    <Popover>
      <PopoverTrigger as-child>
        <Button variant="ghost" class="ml-1 [&_svg]:size-3.5">
          <Filter class="mr-2" />
          Filter
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-full">
        <div class="flex flex-col space-y-5 mb-5">
          <div class="font-semibold leading-none text-sm">
            Status
          </div>
          <div class="grid grid-cols-3 gap-y-6 gap-x-10">
            <div v-for="(label, value) in jobOrderStatuses" :key="value" class="flex items-center gap-x-2">
              <Checkbox :id="value" class="border-gray-400 dark:border-white" />
              <Label :for="value" class="font-normal">{{ label }}</Label>
            </div>
          </div>
        </div>
        <Separator />
        <div class="flex flex-col space-y-5 my-5">
          <div class="font-semibold leading-none text-sm">
            Date of Service
          </div>
          <div class="grid grid-cols-2 gap-10">
            <!--From-->
            <FormField name="from">
              <FormItem class="flex flex-col">
                <FormLabel>
                  <span class="pr-4">
                    From
                  </span>
                  <Popover>
                    <PopoverTrigger as-child>
                      <FormControl>
                        <Button variant="outline" class="w-[240px] ps-3 text-start font-normal text-muted-foreground">
                          <span>Pick a date</span>
                          <Calendar class="ms-auto h-4 w-4 opacity-50" />
                        </Button>
                      </FormControl>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                      <AppCalendar /> 
                    </PopoverContent>
                  </Popover>
                </FormLabel>               
              </FormItem>
            </FormField>
            <!--To-->
            <FormField name="from">
              <FormItem class="flex flex-col">
                <FormLabel>
                  <span class="pr-4">
                    To
                  </span>
                  <Popover>
                    <PopoverTrigger as-child>
                      <FormControl>
                        <Button variant="outline" class="w-[240px] ps-3 text-start font-normal text-muted-foreground">
                          <span>Pick a date</span>
                          <Calendar class="ms-auto h-4 w-4 opacity-50" />
                        </Button>
                      </FormControl>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                      <AppCalendar /> 
                    </PopoverContent>
                  </Popover>
                </FormLabel>               
              </FormItem>
            </FormField>
          </div>
        </div>
        <div class="flex items-center justify-end space-x-2">
          <Button variant="outline">Clear</Button>
          <Button variant="default">Apply Filter</Button>          
        </div>

      </PopoverContent>
    </Popover>
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="ghost" class="ml-1 [&_svg]:size-3.5">
          <ChevronsUpDown class="mr-2" />
          Sort
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent>
        <DropdownMenuLabel>
          Sort by
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuCheckboxItem>

        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="ghost" class="mx-1 [&_svg]:size-3.5">
            <Settings2 class="mr-2" />
            View
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent>
        <DropdownMenuLabel>
          Toggle columns
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuCheckboxItem
            v-for="column in table.getAllColumns().filter((column) => column.getCanHide())" 
            :key="column.id"
            :checked="column.getIsVisible()" 
            @update:checked="(value: boolean) => { column.toggleVisibility(!!value) }">
          {{ column.columnDef.meta?.label }}
        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
    <div class="ml-auto">
      <Button
        :disabled="! hasRowSelection"
        :variant="hasRowSelection ? 'destructive' : 'secondary'"
        class="mx-3">
        <Archive class="mr-2" />
        {{ hasRowSelection ? `Archive (${table.getSelectedRowModel().rows.length})` : 'Archive' }}
      </Button>
      <Button variant="outline" class="">
        <Download class="mr-2" />
        Export 
        <!-- <ChevronDown class="ml-2" /> -->
      </Button>
    </div>
  </div>
  <Table>
    <TableHeader>
      <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id" class="border-t border-b">
          <TableHead
            v-for="(header, index) in headerGroup.headers"
            :key="header.id"
            :class="[
              index === 0 ? 'rounded-l-md' : '',
              index === headerGroup.headers.length - 1 ? 'rounded-r-md' : ''
            ]"
          >
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
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
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
  <div class="flex items-center justify-end space-x-2 px-4 py-3">
    <div class="flex-1 space-x-1.5 text-sm">
      <span class="font-bold">
        {{ `${props.meta.from} - ${props.meta.to}` }}
      </span>
      <span class="text-xs text-muted-foreground">of</span>
      <span class="font-bold">
        {{ table.getRowCount() }}
      </span>
      <span class="text-xs text-muted-foreground">results</span>
    </div>
    <div class="flex items-center space-x-2">
      <span class="text-xs font-medium">
        Showing
      </span>
      <Select
        :modelValue="table.getState().pagination.pageSize.toString()"
        @update:modelValue="(value) => { table.setPageSize(Number(value)); table.setPageIndex(1); }">
        <SelectTrigger class="h-8 w-[80px]">
          <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" class="text-xs font-semibold" />
        </SelectTrigger>
        <SelectContent side="top">
          <SelectItem v-for="pageSize in pageSizes" :key="pageSize.toString()" :value="pageSize.toString()">
            {{ pageSize }}
          </SelectItem>
        </SelectContent>
      </Select>
      <span class="text-xs">
        items per page
      </span>
      <Pagination
        v-slot="{ page }" 
        :items-per-page="table.getState().pagination.pageSize" 
        :total="props.meta.total"
        :page="table.getState().pagination.pageIndex"
        :default-page="1"
        >
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious class="text-xs" @click="table.previousPage()" />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              @click="table.setPageIndex(item.value)"
              class="text-xs py-0"
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === page"
            >{{ item.value }}
            </PaginationItem>

            <PaginationEllipsis
              v-else :key="item.type" 
              :index="index" 
              class="w-9 h-9 flex items-center justify-center">&#8230;
            </PaginationEllipsis>
          </template>

          <PaginationNext @click="table.nextPage()" class="text-xs" />
        </PaginationContent>
      </Pagination>
    </div>
  </div>
</template>
