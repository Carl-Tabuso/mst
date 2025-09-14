<script lang="ts" setup>
import { Button } from '@/components/ui/button'
import type { Employee } from '@/types'
import { Column } from '@tanstack/vue-table'
import { ChevronsUpDown } from 'lucide-vue-next'

interface DataTableHeaderProps {
  column: Column<Employee>
}

const props = defineProps<DataTableHeaderProps>()

const toggleSort = () =>
  props.column.toggleSorting(props.column.getIsSorted() === 'asc')
</script>

<template>
  <Button
    variant="ghost"
    @click="toggleSort"
    class="flex items-center justify-start pl-0 text-[13px] hover:bg-transparent [&_svg]:size-3"
  >
    <span class="">
      {{ props.column.columnDef.meta?.label || '' }}
    </span>
    <ChevronsUpDown class="ml-2" />
  </Button>
</template>
