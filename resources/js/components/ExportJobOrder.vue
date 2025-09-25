<script setup lang="ts">
import { JobOrder } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Download } from 'lucide-vue-next'
import { computed } from 'vue'
import { Badge } from './ui/badge'
import { Button } from './ui/button'

interface ExportJobOrderProps {
  table: Table<JobOrder>
}

const props = defineProps<ExportJobOrderProps>()

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
  <Button
    @click="onExport"
    variant="ghost"
    :disabled="!selectedRowModels.length"
  >
    <Download class="sm:mr-2" />
    <span class="hidden sm:inline">
      Export
    </span>
    <Badge
      v-if="selectedRowModels.length"
      variant="secondary"
      class="rounded-full font-extrabold"
    >
      {{ selectedRowModels.length }}
    </Badge>
  </Button>
</template>
