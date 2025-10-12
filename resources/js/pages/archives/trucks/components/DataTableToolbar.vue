<script setup lang="ts">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import { usePermissions } from '@/composables/usePermissions'
import { Truck } from '@/types'
import { Table } from '@tanstack/vue-table'
import BulkRestoreTruck from './BulkRestoreTruck.vue'
import DateArchivedPopover from './DateArchivedPopover.vue'
import ExportArchivedTruck from './ExportArchivedTruck.vue'
import SearchBar from './SearchBar.vue'

interface DataTableToolbarProps {
  table: Table<Truck>
}

defineProps<DataTableToolbarProps>()
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <SearchBar :table="table" />
    <DateArchivedPopover />
    <ColumnViewToggle :table="table" />
    <ExportArchivedTruck :table="table" />
    <div
      v-if="
        table.getSelectedRowModel().rows.length &&
        usePermissions().can('restore:truck')
      "
      class="ml-auto"
    >
      <BulkRestoreTruck :table="table" />
    </div>
  </div>
</template>
