<script setup lang="ts">
import { computed } from 'vue'

interface JobOrderStats {
  total: number
  by_status: Record<string, number>
}

interface JobOrder {
  id: number | string
  status?: string
  client?: string
  service_area?: string
  created_at?: string
}

const props = defineProps<{
  jobOrderStats?: JobOrderStats | null
  assignedJobOrders: JobOrder[]
  positionName: string
  formatStatus: (status: string) => string
  getStatusColor: (status: string) => string
  averagePerformanceRating?: number | null
}>()

const displayRating = computed(() => {
  return props.averagePerformanceRating || 0
})
</script>

<template>
  <div class="space-y-4 sm:space-y-6">
    <div
      class="rounded-lg bg-white p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6"
    >
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
        Performance Evaluation
      </h3>
      <div
        class="rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 p-4 text-center dark:from-blue-900/20 dark:to-indigo-900/20 sm:p-6"
      >
        <div
          class="mb-2 text-2xl font-bold text-blue-600 dark:text-blue-400 sm:text-3xl"
        >
          {{ displayRating ? displayRating.toFixed(2) : 'N/A' }}
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
          Average Performance Rating
        </div>
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
          {{ displayRating ? 'Out of 5.0' : 'No ratings available' }}
        </div>
      </div>
    </div>

    <div
      v-if="jobOrderStats"
      class="rounded-lg bg-white p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6"
    >
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
        Job Order Statistics
      </h3>
      <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50 sm:p-4">
        <div
          class="mb-4 text-center text-xl font-bold text-blue-600 dark:text-blue-400 sm:text-left sm:text-2xl"
        >
          {{ jobOrderStats.total }} Total Job Orders
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="(count, status) in jobOrderStats.by_status"
            :key="status"
            class="rounded-lg p-3 text-center transition-all hover:scale-105"
            :class="getStatusColor(status)"
          >
            <div class="text-lg font-semibold">{{ count }}</div>
            <div class="truncate text-sm">{{ formatStatus(status) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
