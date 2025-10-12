import { Truck } from '@/types'
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
// import { useArchivedDateRange } from './useArchivedDateRange'

const urlParams = route().queryParams as any

const dataTable = {
  sorting: ref<SortingState>([]),
  columnVisibility: ref<VisibilityState>({
    plateNo: true,
  }),
  rowSelection: ref({}),
  pagination: ref<PaginationState>({
    pageIndex: urlParams.page ? Number(urlParams.page - 1) : 0,
    pageSize: urlParams.per_page ? Number(urlParams.per_page) : 10,
  }),
  globalFilter: ref<string>(urlParams.search ?? ''),
  dateArchived: ref({
    start: urlParams?.filters?.fromDateArchived
      ? parseDate(urlParams.filters.fromDateArchived)
      : undefined,
    end: urlParams?.filters?.toDateArchived
      ? parseDate(urlParams.filters.toDateArchived)
      : undefined,
  }) as Ref<DateRange>,
}

const dataTableStateRequestPayload = computed(() => {
  return {
    page: dataTable.pagination.value.pageIndex + 1,
    per_page: dataTable.pagination.value.pageSize,
    search: dataTable.globalFilter.value,
    filters: {
      fromDateArchived: dataTable.dateArchived.value?.start?.toString(),
      toDateArchived: dataTable.dateArchived.value?.end?.toString(),
    },
  }
})

export function useArchivedTrucksTable() {
  const onSearch = useDebounceFn((table: Table<Truck>, value: string) => {
    table.setGlobalFilter(value)
  }, 500)

  // const { onDateRangePick, onClearDateRange } = useArchivedDateRange(dataTable)

  const onDateArchivedRangePick = (value: DateRange) => {
    dataTable.dateArchived.value = {
      start: value.start,
      end: value.end,
    }
    if (value.start && value.end) {
      applyFilters()
    }
  }

  const onClearDateArchivedRange = () => {
    dataTable.dateArchived.value = {
      start: undefined,
      end: undefined,
    }
    applyFilters()
  }

  const applyFilters = () => {
    router.get(
      route('archive.truck.index'),
      dataTableStateRequestPayload.value,
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    )
  }

  return {
    dataTable,
    onSearch,
    applyFilters,
    onDateArchivedRangePick,
    onClearDateArchivedRange,
  }
}
