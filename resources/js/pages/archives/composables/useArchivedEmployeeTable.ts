import { Employee } from '@/types'
import { router } from '@inertiajs/vue3'
import {
  PaginationState,
  SortingState,
  Table,
  VisibilityState,
} from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { computed, ref } from 'vue'

const urlParams = route().queryParams as any

const dataTable = {
  sorting: ref<SortingState>([]),
  columnVisibility: ref<VisibilityState>({
    fullName: true,
    dateOfBirth: true,
    contactNumber: true,
    createdAt: true,
    archivedAt: true,
    actions: true,
  }),
  rowSelection: ref({}),
  pagination: ref<PaginationState>({
    pageIndex: urlParams.page ? Number(urlParams.page - 1) : 0,
    pageSize: urlParams.per_page ? Number(urlParams.per_page) : 10,
  }),
  globalFilter: ref<string | number>(urlParams.search ?? ''),
}

const dataTableStateRequestPayload = computed(() => {
  return {
    page: dataTable.pagination.value.pageIndex + 1,
    per_page: dataTable.pagination.value.pageSize,
    search: dataTable.globalFilter.value,
  }
})

export function useArchivedEmployeeTable() {
  const onSearch = useDebounceFn(
    (table: Table<Employee>, value: string | number) => {
      table.setGlobalFilter(value)
    },
    500,
  )

  const applyFilters = () => {
    router.get(
      route('archive.employee.index'),
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
  }
}
