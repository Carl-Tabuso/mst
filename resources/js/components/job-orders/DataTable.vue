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
import { Checkbox } from '@/components/ui/checkbox'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { ref, computed, Ref } from 'vue'
import { valueUpdater } from '@/lib/utils'
import { 
  Filter, 
  Settings2, 
  Download, 
  Search,
  Archive,
  Calendar,
} from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { router } from '@inertiajs/vue3'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import AppCalendar from '@/components/AppCalendar.vue'
import { EloquentCollection } from '@/types'
import { Label } from '@/components/ui/label'

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
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: updater => valueUpdater(updater, sorting),
  onColumnVisibilityChange: updater => valueUpdater(updater, columnVisibility),
  onRowSelectionChange: updater => valueUpdater(updater, rowSelection),
  onGlobalFilterChange: updater => {
    valueUpdater(updater, globalFilter)

    table.setPageIndex(1)

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
      page: pageIndex,
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

const hasRowSelection = computed(() => table.getSelectedRowModel().rows.length > 0);

const jobOrderStatuses = computed(() => JobOrderStatuses)

// const fromDos = ref(values.fromDos)
// const toDos = ref(values.toDos)

const filters = ref([]) as Ref

const selected = computed(() => new Set(filters.value))

const applyFilters = () => {
  sendFilterRequest({ statuses: filters.value })
}

const clearFilters = () => {
  filters.value = []
  sendFilterRequest(filters.value)
}

const sendFilterRequest = (filters: any) => {
  router.get(route('job_order.index'), filters, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleStatusSelection = (statusId: string, event: boolean) => {
  if (event) {
    if (! filters.value.includes(statusId)) {
      filters.value.push(statusId)
    }
  } else {
    filters.value = filters.value.filter((id: string) => id !== statusId)
  }
}
</script>

<template>
  <div class="flex items-center py-1">
    <div class="relative w-full max-w-sm items-center">
      <Input
        id="search"
        type="text" 
        class="max-w-sm pl-12 h-9"
        placeholder="Search"
        :model-value="globalFilter"
        @update:model-value="(value) => table.setGlobalFilter(String(value))" />
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
        <!-- <form @submit="onSubmit"> -->
          <div class="flex flex-col space-y-5 mb-5">
            <div class="font-semibold leading-none text-sm">
              Status
            </div>
            <div class="grid grid-cols-3 gap-y-6 gap-x-10">
              <!-- <FormField
                v-for="status in jobOrderStatuses"
                v-slot="{ value, handleChange }"
                :key="status.id"
                type="checkbox"
                :value="status.id"
                :unchecked-value="false"
                name="filters"> -->
                  <!-- <FormItem> -->
                    <div v-for="status in jobOrderStatuses" :key="status.id" class="flex items-center gap-x-2">
                      <!-- <FormControl> -->
                      <Checkbox
                        :id="status.id"
                        :checked="selected.has(status.id)"
                        @update:checked="(event) => handleStatusSelection(status.id, event)"
                        class="border-gray-400 dark:border-white" />
                      <!-- </FormControl> -->
                      <Label :for="status.id" class="font-normal">
                        {{ status.label }}
                      </Label>
                    </div>                    
                  <!-- </FormItem> -->
              <!-- </FormField> -->
            </div>
          </div>
          <Separator />
          <div class="flex flex-col space-y-5 my-5">
            <div class="font-semibold leading-none text-sm">
              Date of Service
            </div>
            <div class="grid grid-cols-2 gap-10">
              <!--From-->
              <!-- <FormField name="from">
                <FormItem class="flex flex-col">
                  <FormLabel>
                    <span class="pr-4">
                      From
                    </span>
                    <Popover>
                      <PopoverTrigger as-child>
                        <FormControl>
                          <Button
                            variant="outline" 
                            :class="['w-[240px] ps-3 text-start font-normal',
                              ! values.fromDos ? 'text-muted-foreground' : '']">
                            <span>
                              {{
                                values.fromDos
                                  ? new Date(values.fromDos).toLocaleDateString('en-ph', {
                                      year: 'numeric',
                                      month: 'long',
                                      day: 'numeric',
                                  })
                                  : 'Pick a date' }}
                            </span>
                            <Calendar class="ms-auto h-4 w-4 opacity-50" />
                          </Button>
                        </FormControl>
                      </PopoverTrigger>
                      <PopoverContent class="w-auto p-0">
                        <AppCalendar
                          :model-value="fromDos"
                          @update:model-value="(value) => setFieldValue('fromDos', value?.toString())" 
                        />
                      </PopoverContent>
                    </Popover>
                  </FormLabel>
                </FormItem>
              </FormField> -->
              <!--To-->
              <!-- <FormField name="to">
                <FormItem class="flex flex-col">
                  <FormLabel>
                    <span class="pr-4">
                      To
                    </span>
                    <Popover>
                      <PopoverTrigger as-child>
                        <FormControl>
                          <Button
                            variant="outline" 
                            :class="['w-[240px] ps-3 text-start font-normal',
                              ! values.toDos ? 'text-muted-foreground' : '']">
                            <span>
                              {{
                                values.toDos
                                  ? new Date(values.toDos).toLocaleDateString('en-ph', {
                                      year: 'numeric',
                                      month: 'long',
                                      day: 'numeric',
                                  })
                                  : 'Pick a date' }}
                            </span>
                            <Calendar class="ms-auto h-4 w-4 opacity-50" />
                          </Button>
                        </FormControl>
                      </PopoverTrigger>
                      <PopoverContent class="w-auto p-0">
                        <AppCalendar
                          :model-value="toDos"
                          @update:model-value="(value) => setFieldValue('toDos', value?.toString())" 
                        />
                      </PopoverContent>
                    </Popover>
                  </FormLabel>
                </FormItem>
              </FormField> -->
            </div>
          </div>
          <div class="flex items-center justify-end space-x-2">
            <!-- <Button type="button" variant="outline">Clear</Button>
            <Button type="submit" variant="default">Apply Filter</Button> -->
            <Button @click="clearFilters" variant="outline">Clear</Button>
            <Button @click="applyFilters" variant="default">Apply Filter</Button>
          </div>
        <!-- </form> -->
      </PopoverContent>
    </Popover>
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
            @update:checked="(value: boolean) => { column.toggleVisibility(!!value) }"
          >{{ column.columnDef.meta?.label }}
        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
      <Button variant="ghost" class="[&_svg]:size-3.5">
        <Download class="mr-2" />
        Export 
        <!-- <ChevronDown class="ml-2" /> -->
      </Button>
    <div class="ml-auto">
      <Button
        :disabled="! hasRowSelection"
        :variant="hasRowSelection ? 'destructive' : 'secondary'"
        class="mx-3">
        <Archive class="mr-2" />
        {{ hasRowSelection ? `Archive (${table.getSelectedRowModel().rows.length})` : 'Archive' }}
      </Button>
    </div>
  </div>
  <Table>
    <TableHeader>
      <TableRow 
        v-for="headerGroup in table.getHeaderGroups()" 
        :key="headerGroup.id" 
        class="border-t border-b">
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
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="border-b py-3">
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
        {{ props.meta.total }}
      </span>
      <span class="text-xs text-muted-foreground">results</span>
    </div>
    <div class="flex items-center space-x-2">
      <span class="text-xs font-medium">
        Showing
      </span>
      <Select
        :model-value="table.getState().pagination.pageSize.toString()"
        @update:model-value="(value) => { table.setPageSize(Number(value)); table.setPageIndex(1); }">
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
