<script setup lang="ts">
import { Employee } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Download } from 'lucide-vue-next'
import { computed } from 'vue'
import { Badge } from './ui/badge'
import { Button } from './ui/button'

interface ExportEmployeeProps {
  table: Table<Employee>
}

const props = defineProps<ExportEmployeeProps>()

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const onExport = () => {
  window.open(
    route('archive.employee.export', {
      employeeIds: selectedRowModels.value.map((row) => row.original.id),
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
