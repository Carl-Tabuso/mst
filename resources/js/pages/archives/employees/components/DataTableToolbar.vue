<script setup lang="ts">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import ExportEmployee from '@/components/ExportEmployee.vue'
import { usePermissions } from '@/composables/usePermissions'
import { Employee } from '@/types'
import { Table } from '@tanstack/vue-table'
import BulkRestoreEmployee from './BulkRestoreEmployee.vue'
import DateArchivedPopover from './DateArchivedPopover.vue'
import PositionPopoverFilter from './PositionPopoverFilter.vue'
import SearchBar from './SearchBar.vue'

interface DataTableToolbarProps {
  table: Table<Employee>
}

defineProps<DataTableToolbarProps>()

const canBulkRestoreEmployee = usePermissions().can('manage:employees')
</script>

<template>
  <div class="flex flex-row items-center py-1">
    <SearchBar :table="table" />
    <PositionPopoverFilter />
    <DateArchivedPopover />
    <ColumnViewToggle :table="table" />
    <ExportEmployee :table="table" />
    <div
      v-if="table.getSelectedRowModel().rows.length && canBulkRestoreEmployee"
      class="ml-auto"
    >
      <BulkRestoreEmployee :table="table" />
    </div>
  </div>
</template>
