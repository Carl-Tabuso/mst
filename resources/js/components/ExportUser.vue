<script setup lang="ts">
import { User } from '@/types'
import { Table } from '@tanstack/vue-table'
import { Download } from 'lucide-vue-next'
import { computed } from 'vue'
import { Badge } from './ui/badge'
import { Button } from './ui/button'

interface ExportUserProps {
  table: Table<User>
}

const props = defineProps<ExportUserProps>()

const selectedRowModels = computed(() => props.table.getSelectedRowModel().rows)

const onExport = () => {
  window.open(
    route('archive.user.export', {
      userIds: selectedRowModels.value.map((row) => row.original.id),
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
