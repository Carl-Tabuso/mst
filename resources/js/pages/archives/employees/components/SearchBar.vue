<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Employee } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Search } from 'lucide-vue-next'
import { useArchivedEmployeeTable } from '../../composables/useArchivedEmployeeTable'

interface DataTableToolbarProps {
  table: Table<Employee>
}

defineProps<DataTableToolbarProps>()

const { onSearch, dataTable } = useArchivedEmployeeTable()
</script>

<template>
  <div class="relative w-full max-w-xs items-center">
    <Input
      id="search"
      type="text"
      class="h-9 max-w-sm pl-12"
      placeholder="Search"
      :model-value="dataTable.globalFilter.value"
      @update:model-value="(value) => onSearch(table, value)"
    />
    <span
      class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
    >
      <Search class="size-4 text-muted-foreground" />
    </span>
  </div>
</template>
