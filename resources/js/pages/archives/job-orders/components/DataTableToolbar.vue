<script setup lang="ts" generic="TData">
import ColumnViewToggle from '@/components/ColumnViewToggle.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { usePermissions } from '@/composables/usePermissions'
import { JobOrder } from '@/types'
import type { Table } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { Download, Search } from 'lucide-vue-next'
import { computed } from 'vue'
import BulkRestoreJobOrder from './BulkRestoreJobOrder.vue'
import FilterPopover from './FilterPopover.vue'

interface DataTableToolbarProps {
  table: Table<JobOrder>
  globalFilter: string | number
}

const props = defineProps<DataTableToolbarProps>()

const onSearch = useDebounceFn((value: string | number) => {
  props.table.setGlobalFilter(value)
}, 500)

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const onExport = () => {
  window.open(
    route('job_order.export', {
      jobOrderIds: selectedRowModels.value.map((row) => row.original.id),
    }),
    '_blank',
  )

  props.table.resetRowSelection()
}
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
        @update:model-value="onSearch"
      />
      <span
        class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
      >
        <Search class="size-4 text-muted-foreground" />
      </span>
    </div>
    <FilterPopover />
    <ColumnViewToggle :table="table" />
    <Button
      @click="onExport"
      variant="ghost"
      :disabled="!selectedRowModels.length"
    >
      <Download class="mr-2" />
      Export
      <Badge
        v-if="selectedRowModels.length"
        variant="secondary"
        class="rounded-full font-extrabold"
      >
        {{ selectedRowModels.length }}
      </Badge>
    </Button>
    <div
      v-if="
        selectedRowModels.length &&
        usePermissions().can('restore:archived_job_order')
      "
      class="ml-auto"
    >
      <BulkRestoreJobOrder :table="table" />
    </div>
  </div>
</template>
