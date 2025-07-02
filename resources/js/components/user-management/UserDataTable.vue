<script setup lang="ts">
import { computed } from 'vue'
import { useVueTable, getCoreRowModel } from '@tanstack/vue-table'
import DataTable from '@/components/tasks/components/DataTable.vue'
import DataTableToolbar from '@/components/tasks/components/DataTableToolbar.vue'
import DataTablePagination from '@/components/tasks/components/DataTablePagination.vue'

const props = defineProps<{
  columns: any[]
  data: any[]
  isLoading?: boolean
}>()

// Use getter function for reactive data
const table = useVueTable({
  get data() { return props.data || [] }, // Reactive data access
  get columns() { return props.columns }, // Reactive columns access
  getCoreRowModel: getCoreRowModel(),
})
</script>

<template>
  <div class="space-y-4">
    <DataTableToolbar :table="table" />
    <div class="rounded-md border">
      <DataTable 
        :table="table" 
        :columns="columns"
        :isLoading="isLoading"
      />
    </div>
    <DataTablePagination :table="table" />
  </div>
</template>