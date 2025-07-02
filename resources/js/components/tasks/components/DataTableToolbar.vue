<script setup lang="ts">
import { computed } from 'vue'
import { CrossIcon } from 'lucide-vue-next'
import {  Button } from '@/components/ui/button'
import {  Input } from '@/components/ui/input'

import DataTableFacetedFilter from './DataTableFacetedFilter.vue'
import { statuses } from '@/types/user'

const props = defineProps<{
  table: any
}>()

const isFiltered = computed(() => {
  return props.table?.getState?.()?.columnFilters?.length > 0
})

const emailColumn = computed(() => props.table.getColumn('email'))
const statusColumn = computed(() => props.table.getColumn('status'))
</script>

<template>
  <div class="flex items-center justify-between">
    <div class="flex flex-1 items-center space-x-2">
      <Input
        v-if="emailColumn"
        placeholder="Filter users..."
        :model-value="emailColumn.getFilterValue() ?? ''"
        class="h-8 w-[150px] lg:w-[250px]"
        @update:model-value="emailColumn.setFilterValue"
      />
      
      <DataTableFacetedFilter
        v-if="statusColumn"
        :column="statusColumn"
        title="Status"
        :options="statuses"
      />

      <Button
        v-if="isFiltered"
        variant="ghost"
        class="h-8 px-2 lg:px-3"
        @click="table.resetColumnFilters()"
      >
        Reset
        <CrossIcon class="ml-2 h-4 w-4" />
      </Button>
    </div>
  </div>
</template>