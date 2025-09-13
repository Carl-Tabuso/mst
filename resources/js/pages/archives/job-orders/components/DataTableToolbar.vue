<script setup lang="ts" generic="TData">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import ExportJobOrder from '@/components/ExportJobOrder.vue'
import { usePermissions } from '@/composables/usePermissions'
import { JobOrder } from '@/types'
import type { Table } from '@tanstack/vue-table'
import BulkRestoreJobOrder from './BulkRestoreJobOrder.vue'
import FilterPopover from './FilterPopover.vue'
import SearchBar from './SearchBar.vue'

interface DataTableToolbarProps {
  table: Table<JobOrder>
}

defineProps<DataTableToolbarProps>()
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <SearchBar :table="table" />
    <FilterPopover />
    <ColumnViewToggle :table="table" />
    <ExportJobOrder :table="table" />
    <div
      v-if="
        table.getSelectedRowModel().rows.length &&
        usePermissions().can('restore:archived_job_order')
      "
      class="ml-auto"
    >
      <BulkRestoreJobOrder :table="table" />
    </div>
  </div>
</template>
