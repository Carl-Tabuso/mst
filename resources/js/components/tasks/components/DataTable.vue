<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { FlexRender } from '@tanstack/vue-table'
import { onMounted } from 'vue'

const props = defineProps<{
  table: any
  columns: any[]
  isLoading?: boolean
}>()
onMounted(() => {
  if (props.table) {
  }
})
</script>

<template>
  <Table>
    <TableHeader>
      <TableRow
        v-for="headerGroup in table.getHeaderGroups()"
        :key="headerGroup.id"
      >
        <TableHead
          v-for="header in headerGroup.headers"
          :key="header.id"
        >
          <FlexRender
            v-if="!header.isPlaceholder"
            :render="header.column.columnDef.header"
            :props="header.getContext()"
          />
        </TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <template v-if="isLoading">
        <TableRow
          v-for="i in 5"
          :key="i"
        >
          <TableCell
            v-for="col in columns"
            :key="col.id"
          >
            <Skeleton class="h-4 w-[100px]" />
          </TableCell>
        </TableRow>
      </template>
      <template v-else-if="table.getRowModel().rows?.length">
        <TableRow
          v-for="row in table.getRowModel().rows"
          :key="row.id"
          :data-state="row.getIsSelected() && 'selected'"
        >
          <TableCell
            v-for="cell in row.getVisibleCells()"
            :key="cell.id"
          >
            <FlexRender
              :render="cell.column.columnDef.cell"
              :props="cell.getContext()"
            />
          </TableCell>
        </TableRow>
      </template>
      <TableRow v-else>
        <TableCell
          :colspan="columns.length"
          class="h-24 text-center"
        >
          No results found.
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
