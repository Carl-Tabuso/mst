import { User } from '@/types'
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
  columnVisibility: ref<VisibilityState>({}),
  rowSelection: ref({}),
  pagination: ref<PaginationState>(),
  globalFilter: ref<string | number>(''),
}

export function useArchivedUserTable() {
  const onSearch = useDebounceFn(
    (table: Table<User>, value: string | number) => {
      table.setGlobalFilter(value)
    },
    500,
  )

  return {
    dataTable,
    onSearch,
  }
}
