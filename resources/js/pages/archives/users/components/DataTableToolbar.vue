<script setup lang="ts">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import { Input } from '@/components/ui/input'
import { usePermissions } from '@/composables/usePermissions'
import { User } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Search } from 'lucide-vue-next'
import { computed } from 'vue'
import { useArchivedUserTable } from '../../composables/useArchivedUserTable'
import BulkRestoreUser from './BulkRestoreUser.vue'

interface DataTableToolbarProps {
  table: Table<User>
  globalFilter: string | number
}

const props = defineProps<DataTableToolbarProps>()

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const { onSearch } = useArchivedUserTable()
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <div class="relative w-full max-w-xs items-center">
      <Input
        id="search"
        type="text"
        class="h-9 max-w-sm pl-12"
        placeholder="Search"
        :model-value="globalFilter"
        @update:model-value="(value) => onSearch(table, value)"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>
    <ColumnViewToggle :table="table" />
    <div
      v-if="selectedRowModels.length && usePermissions().can('manage:users')"
      class="ml-auto"
    >
      <BulkRestoreUser :table="table" />
    </div>
  </div>
</template>
