import { usePermissions } from '@/composables/usePermissions'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import { parseDate } from '@internationalized/date'
import {
  PaginationState,
  SortingState,
  Table,
  VisibilityState,
} from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { DateRange } from 'reka-ui'
import { computed, Ref, ref } from 'vue'

const urlParams = route().queryParams as any

const dataTable = {
  sorting: ref<SortingState>([]),
  columnVisibility: ref<VisibilityState>({
    id: true,
    client: false,
    contactNo: false,
    address: false,
    status: true,
    creator: true,
    dateTime: false,
    archive: true,
  }),
  rowSelection: ref({}),
  pagination: ref<PaginationState>({
    pageIndex: urlParams.page ? Number(urlParams.page - 1) : 0,
    pageSize: urlParams.per_page ? Number(urlParams.per_page) : 10,
  }),
  globalFilter: ref<string | number>(urlParams.search ?? ''),
  statuses: ref<string[]>(urlParams?.filters?.statuses ?? []),
  dateOfServiceRange: ref({
    start: urlParams?.filters?.fromDateOfService
      ? parseDate(urlParams.filters.fromDateOfService)
      : undefined,
    end: urlParams?.filters?.toDateOfService
      ? parseDate(urlParams.filters.toDateOfService)
      : undefined,
  }) as Ref<DateRange>,
}

const dataTableStateRequestPayload = computed(() => {
  return {
    page: dataTable.pagination.value.pageIndex + 1,
    per_page: dataTable.pagination.value.pageSize,
    search: dataTable.globalFilter.value,
    filters: {
      statuses: dataTable.statuses.value,
      fromDateOfService: dataTable.dateOfServiceRange.value?.start?.toString(),
      toDateOfService: dataTable.dateOfServiceRange.value?.end?.toString(),
    },
  }
})

export function useJobOrderTable() {
  dataTable.columnVisibility.value.archive =
    usePermissions().can('update:job_order')

  const onSearch = useDebounceFn(
    (table: Table<JobOrder>, value: string | number) => {
      table.setGlobalFilter(value)
      table.setPageIndex(0)
    },
    500,
  )

  const onStatusSelect = (status: string, selected: boolean) => {
    const statuses = dataTable.statuses.value
    if (selected && !statuses.includes(status)) {
      statuses.push(status)
    } else {
      const index = statuses.findIndex(
        (existingStatus: string) => existingStatus === status,
      )
      statuses.splice(index, 1)
    }
  }

  const applyFilters = () => {
    router.get(route('job_order.index'), dataTableStateRequestPayload.value, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }

  const clearFilters = () => {
    dataTable.statuses.value = []
    dataTable.dateOfServiceRange.value = {
      start: undefined,
      end: undefined,
    }
    dataTable.pagination.value.pageIndex = 0
    applyFilters()
  }

  const onDateOfServiceRangePick = (value: DateRange) => {
    dataTable.dateOfServiceRange.value = {
      start: value.start,
      end: value.end,
    }
  }

  return {
    dataTable,
    dataTableStateRequestPayload,
    onSearch,
    onStatusSelect,
    onDateOfServiceRangePick,
    applyFilters,
    clearFilters,
  }
}
