import { Employee } from '@/types'
import {
  PaginationState,
  SortingState,
  Table,
  VisibilityState,
} from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { ref } from 'vue'

const urlParams = route().queryParams

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
  pagination: ref<PaginationState>(),
  globalFilter: ref<string | number>(''),
}

export function useArchivedEmployeeTable() {
  const onSearch = useDebounceFn(
    (table: Table<Employee>, value: string | number) => {
      table.setGlobalFilter(value)
    },
    500,
  )

  return {
    dataTable,
    onSearch,
  }
}
