<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Truck } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Download } from 'lucide-vue-next'
import { computed } from 'vue'

interface ExportArchivedTruckProps {
  table: Table<Truck>
}

const props = defineProps<ExportArchivedTruckProps>()

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const onExport = () => {
  window.location.href = route('archive.truck.export', {
    truckIds: selectedRowModels.value.map((row) => row.original.id),
  })
  props.table.resetRowSelection()
}
</script>

<template>
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
</template>
