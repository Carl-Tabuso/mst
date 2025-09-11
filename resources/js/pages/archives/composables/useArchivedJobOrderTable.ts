import { router } from '@inertiajs/vue3'
import { parseDate } from '@internationalized/date'
import {
  PaginationState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import { DateRange } from 'reka-ui'
import { computed, Ref, ref } from 'vue'

const urlParams = route().queryParams as any

const dataTable = {
  sorting: ref<SortingState>([]),
  columnVisibility: ref<VisibilityState>({
    ticket: true,
    client: false,
    serviceableType: true,
    status: true,
    creator: true,
    dateTime: false,
    archivedAt: true,
    actions: true,
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
  dateArchivedRange: ref({
    start: urlParams?.filters?.fromDateOfArchived
      ? parseDate(urlParams.filters.fromDateOfArchived)
      : undefined,
    end: urlParams?.filters?.toDateOfArchived
      ? parseDate(urlParams.filters.toDateOfArchived)
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
      fromDateOfArchived: dataTable.dateArchivedRange.value?.start?.toString(),
      toDateOfArchived: dataTable.dateArchivedRange.value?.end?.toString(),
    },
  }
})

export function useArchivedJobOrderTable() {
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
    router.get(
      route('archive.job_order.index'),
      dataTableStateRequestPayload.value,
      {
        preserveState: true,
        replace: true,
      },
    )
  }

  const clearFilters = () => {
    dataTable.statuses.value = []
    dataTable.dateOfServiceRange.value.start = undefined
    dataTable.dateOfServiceRange.value.end = undefined
    dataTable.dateArchivedRange.value.start = undefined
    dataTable.dateArchivedRange.value.end = undefined
    dataTable.pagination.value.pageIndex = 0

    applyFilters()
  }

  const onDateOfServiceRangePick = (value: DateRange) => {
    dataTable.dateOfServiceRange.value.start = value.start
    dataTable.dateOfServiceRange.value.end = value.end
  }

  const onDateArchivedRangePick = (value: DateRange) => {
    dataTable.dateArchivedRange.value.start = value.start
    dataTable.dateArchivedRange.value.end = value.end
  }

  return {
    dataTable,
    dataTableStateRequestPayload,
    onStatusSelect,
    applyFilters,
    clearFilters,
    onDateOfServiceRangePick,
    onDateArchivedRangePick,
  }
}
