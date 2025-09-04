<template>
  <div
    class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <!-- Left side - Selection count -->
    <div class="flex-1 flex justify-start">
      <p class="text-sm text-gray-700 dark:text-gray-300">
        {{ selectedCount }} of {{ filteredCount }} row(s) selected.
      </p>
    </div>

    <!-- Right side - Pagination controls -->
    <div class="flex items-center space-x-6 lg:space-x-8">
      <!-- Rows per page -->
      <div class="flex items-center space-x-2">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Rows per page</p>
        <select :value="currentPerPage" @change="handlePerPageChange($event.target.value)"
          class="h-8 w-16 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm px-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
          <option v-for="size in pageSizes" :key="size" :value="size">
            {{ size }}
          </option>
        </select>
      </div>

      <!-- Page indicator -->
      <div class="flex w-[100px] items-center justify-center text-sm font-medium text-gray-700 dark:text-gray-300">
        Page {{ currentPage }} of {{ lastPage }}
      </div>

      <!-- Navigation buttons -->
      <div class="flex items-center space-x-2">
        <!-- First page -->
        <button type="button" :disabled="currentPage === 1" @click="goToPage(1)"
          class="hidden lg:inline-flex items-center justify-center h-8 w-8 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          title="Go to first page">
          <ChevronsLeft class="h-4 w-4" />
        </button>

        <!-- Previous page -->
        <button type="button" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)"
          class="inline-flex items-center justify-center h-8 w-8 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          title="Go to previous page">
          <ChevronLeft class="h-4 w-4" />
        </button>

        <!-- Next page -->
        <button type="button" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)"
          class="inline-flex items-center justify-center h-8 w-8 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          title="Go to next page">
          <ChevronRight class="h-4 w-4" />
        </button>

        <!-- Last page -->
        <button type="button" :disabled="currentPage === lastPage" @click="goToPage(lastPage)"
          class="hidden lg:inline-flex items-center justify-center h-8 w-8 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          title="Go to last page">
          <ChevronsRight class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'

interface PaginationProps {
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from?: number
    to?: number
  }
  selectedCount?: number
  filteredCount?: number
}

const props = withDefaults(defineProps<PaginationProps>(), {
  selectedCount: 0,
  filteredCount: 0
})

const emit = defineEmits<{
  pageChange: [page: number]
  perPageChange: [size: number]
}>()

const pageSizes = [10, 25, 50, 100]

const currentPage = computed(() => props.pagination.current_page)
const lastPage = computed(() => props.pagination.last_page || 1)
const currentPerPage = computed(() => props.pagination.per_page)

const goToPage = (page: number) => {
  if (page >= 1 && page <= lastPage.value && page !== currentPage.value) {
    emit('pageChange', page)
  }
}

const handlePerPageChange = (value: string | number) => {
  const size = typeof value === 'string' ? parseInt(value) : value
  if (size && size !== currentPerPage.value) {
    emit('perPageChange', size)
  }
}
</script>