<script setup lang="ts">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import ExportUser from '@/components/ExportUser.vue'
import { usePermissions } from '@/composables/usePermissions'
import { User } from '@/types'
import { Table } from '@tanstack/vue-table'
import BulkRestoreUser from './BulkRestoreUser.vue'
import SearchBar from './SearchBar.vue'

interface DataTableToolbarProps {
  table: Table<User>
}

defineProps<DataTableToolbarProps>()
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <SearchBar :table="table" />
    <ColumnViewToggle :table="table" />
    <ExportUser :table="table" />
    <div
      v-if="
        table.getSelectedRowModel().rows.length &&
        usePermissions().can('manage:users')
      "
      class="ml-auto"
    >
      <BulkRestoreUser :table="table" />
    </div>
  </div>
</template>
