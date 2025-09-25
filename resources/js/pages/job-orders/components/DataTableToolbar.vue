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
  <div class="flex flex-col gap-1 sm:flex-row sm:gap-0 sm:py-1">
    <SearchBar :table="table" />
    <div class="flex w-full items-center">
      <div class="flex items-center">
        <FilterPopover :routeName="routeName" />
        <ColumnViewToggle :table="table" />
        <ExportJobOrder :table="table" />
      </div>
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
  </div>
</template>
