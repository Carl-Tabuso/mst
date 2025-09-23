<script setup lang="ts">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import ExportJobOrder from '@/components/ExportJobOrder.vue'
import { usePermissions } from '@/composables/usePermissions'
import { JobOrder } from '@/types'
import type { Table } from '@tanstack/vue-table'
import BulkArchiveJobOrder from './BulkArchiveJobOrder.vue'
import FilterPopover from './FilterPopover.vue'
import SearchBar from './SearchBar.vue'

interface DataTableToolbarProps {
  table: Table<JobOrder>
  routeName?: string
}

defineProps<DataTableToolbarProps>()
</script>

<template>
  <div class="flex items-center py-1">
    <SearchBar :table="table" />
    <FilterPopover :routeName="routeName" />
    <ColumnViewToggle :table="table" />
    <ExportJobOrder :table="table" />
    <div
      v-if="
        table.getSelectedRowModel().rows.length &&
        usePermissions().can('update:job_order')
      "
      class="ml-auto"
    >
      <BulkArchiveJobOrder :table="table" />
    </div>
  </div>
</template>
