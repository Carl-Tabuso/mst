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
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-8 p-3">
    <div class="flex items-center gap-2">
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
        Rows per page
      </p>
      <Select :model-value="table.getState().pagination.pageSize"
        @update:model-value="(value) => table.setPageSize(Number(value))">
        <SelectTrigger class="h-8 w-[70px] dark:bg-zinc-900 dark:border-gray-700 dark:text-gray-200">
          <SelectValue :placeholder="table.getState().pagination.pageSize.toString()" />
        </SelectTrigger>
        <SelectContent side="top" class="dark:bg-zinc-900 dark:text-gray-200 dark:border-gray-700">
          <SelectItem v-for="pageSize in pageSizes" :key="pageSize" :value="pageSize">
            {{ pageSize }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <div class="flex items-center gap-4">
      <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
        Page {{ currentPage }} of {{ lastPage }}
      </div>
      <div class="flex items-center gap-2">
        <Button variant="outline" class="hidden h-8 w-8 p-0 sm:flex dark:border-gray-700 dark:text-gray-200"
          :disabled="!canGoPrevPage" @click="table.setPageIndex(0)">
          <span class="sr-only">Go to first page</span>
          <ChevronsLeft class="h-4 w-4" />
        </Button>
        <Button variant="outline" class="h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200" :disabled="!canGoPrevPage"
          @click="table.previousPage()">
          <span class="sr-only">Go to previous page</span>
          <ChevronLeftIcon class="h-4 w-4" />
        </Button>
        <Button variant="outline" class="h-8 w-8 p-0 dark:border-gray-700 dark:text-gray-200" :disabled="!canGoNextPage"
          @click="table.nextPage()">
          <span class="sr-only">Go to next page</span>
          <ChevronRightIcon class="h-4 w-4" />
        </Button>
        <Button variant="outline" class="hidden h-8 w-8 p-0 sm:flex dark:border-gray-700 dark:text-gray-200"
          :disabled="!canGoNextPage" @click="table.setPageIndex(table.getPageCount() - 1)">
          <span class="sr-only">Go to last page</span>
          <ChevronsRight class="h-4 w-4" />
        </Button>
      </div>

    </div>
  </div>
</template>
