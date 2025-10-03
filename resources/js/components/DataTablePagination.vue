<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import type { Table } from '@tanstack/vue-table'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronsLeft,
  ChevronsRight,
} from 'lucide-vue-next'
import { computed } from 'vue'

interface DataTablePaginationProps {
  table: Table<any>
  lastPage: number
}

const props = defineProps<DataTablePaginationProps>()

const pageSizes = [10, 25, 50, 100]

const currentPage = computed(
  () => props.table.getState().pagination.pageIndex + 1,
)

const canGoPrevPage = computed(
  () => props.table.getState().pagination.pageIndex > 0,
)
const canGoNextPage = computed(() => currentPage.value < props.lastPage)
</script>

<template>
  <div
    class="flex flex-col gap-4 p-3 sm:flex-row sm:items-center sm:justify-between"
  >
    <div class="text-sm text-muted-foreground sm:flex-1">
      {{ table.getFilteredSelectedRowModel().rows.length }} of
      {{ table.getFilteredRowModel().rows.length }} row(s) selected.
    </div>

    <div
      class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6 lg:gap-8"
    >
      <div class="flex items-center justify-between gap-2 sm:justify-start">
        <p class="whitespace-nowrap text-sm font-medium">Rows per page</p>
        <Select
          :model-value="table.getState().pagination.pageSize"
          @update:model-value="(value) => table.setPageSize(Number(value))"
        >
          <SelectTrigger class="h-8 w-[70px] sm:w-[80px]">
            <SelectValue
              :placeholder="table.getState().pagination.pageSize.toString()"
            />
          </SelectTrigger>
          <SelectContent
            side="top"
            class="min-w-fit"
          >
            <SelectItem
              v-for="pageSize in pageSizes"
              :key="pageSize"
              :value="pageSize"
              class="pr-10"
            >
              {{ pageSize }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="flex items-center justify-center sm:justify-start">
        <div
          class="flex w-[100px] items-center justify-center whitespace-nowrap text-sm font-medium"
        >
          <span class="sm:hidden">{{ currentPage }}/{{ lastPage }}</span>
          <span class="hidden sm:inline"
            >Page {{ currentPage }} of {{ lastPage }}</span
          >
        </div>
      </div>

      <div class="flex items-center justify-center gap-1 sm:gap-2">
        <Button
          variant="outline"
          class="hidden h-8 w-8 p-0 sm:flex"
          :disabled="!canGoPrevPage"
          @click="table.setPageIndex(0)"
        >
          <span class="sr-only">Go to first page</span>
          <ChevronsLeft class="h-4 w-4" />
        </Button>

        <Button
          variant="outline"
          class="h-8 w-8 p-0"
          :disabled="!canGoPrevPage"
          @click="table.previousPage()"
        >
          <span class="sr-only">Go to previous page</span>
          <ChevronLeftIcon class="h-4 w-4" />
        </Button>

        <Button
          variant="outline"
          class="h-8 w-8 p-0"
          :disabled="!canGoNextPage"
          @click="table.nextPage()"
        >
          <span class="sr-only">Go to next page</span>
          <ChevronRightIcon class="h-4 w-4" />
        </Button>

        <Button
          variant="outline"
          class="hidden h-8 w-8 p-0 sm:flex"
          :disabled="!canGoNextPage"
          @click="table.setPageIndex(table.getPageCount() - 1)"
        >
          <span class="sr-only">Go to last page</span>
          <ChevronsRight class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>
