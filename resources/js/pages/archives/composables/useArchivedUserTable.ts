import { User } from '@/types'
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
    emailVerifiedAt: true,
    roles: true,
    createdAt: false,
    deletedAt: true,
  }),
  rowSelection: ref({}),
  pagination: ref<PaginationState>({
    pageIndex: urlParams.page ? Number(urlParams.page - 1) : 0,
    pageSize: urlParams.per_page ? Number(urlParams.per_page) : 10,
  }),
  globalFilter: ref<string | number>(urlParams.search ?? ''),
  roles: ref<string[]>(urlParams?.filters?.roles?.map() ?? []),
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
      roles: dataTable.roles.value,
      fromDateArchived: dataTable.dateArchived.value?.start?.toString(),
      toDateArchived: dataTable.dateArchived.value?.end?.toString(),
    },
  }
})

export function useArchivedUserTable() {
  const onSearch = useDebounceFn(
    (table: Table<User>, value: string | number) => {
      table.setGlobalFilter(value)
    },
    500,
  )

  const onRoleSelect = (role: string) => {
    const roles = dataTable.roles.value
    if (!roles.includes(role)) {
      roles.push(role)
    } else {
      const index = roles.findIndex((r) => role === r)
      roles.splice(index, 1)
    }
    applyFilters()
  }

  const onClearRoles = () => {
    dataTable.roles.value = []
    applyFilters()
  }

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
      route('archive.user.index'),
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
    onRoleSelect,
    onClearRoles,
    onDateArchivedRangePick,
    onClearDateArchivedRange,
    applyFilters,
  }
}
