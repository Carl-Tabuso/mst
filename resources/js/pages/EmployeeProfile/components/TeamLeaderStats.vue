<script setup lang="ts">
import { computed } from 'vue'

interface TeamStats {
  teams_managed: number
  total_team_job_orders: number
  team_success_rate: number
}

interface JobOrder {
  id: number | string
  status?: string
  client?: string
  service_area?: string
  created_at?: string
}

const props = defineProps<{
  teamStats?: TeamStats | null
  assignedJobOrders: JobOrder[]
  averagePerformanceRating?: number | null
  formatStatus: (status: string) => string
  getStatusColor: (status: string) => string
}>()

const displayRating = computed(() => {
  return props.averagePerformanceRating || 0
})
</script>

<template>
  <div class="space-y-4 sm:space-y-6">
    <div
      v-if="teamStats || averagePerformanceRating"
      class="rounded-lg bg-white p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6"
    >
      <div
        class="space-y-6 lg:grid lg:grid-cols-1 lg:gap-6 lg:space-y-0 xl:grid-cols-2"
      >
        <div
          v-if="teamStats"
          class="space-y-4"
        >
          <h3 class="text-lg font-semibold text-sky-900 dark:text-sky-700">
            Team Leadership Stats
          </h3>

          <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
            <div
              class="rounded-lg bg-blue-50 p-3 text-center transition-all hover:scale-105 dark:bg-blue-900/20 sm:p-4"
            >
              <div
                class="text-xl font-bold text-blue-600 dark:text-blue-400 sm:text-2xl"
              >
                {{ teamStats.teams_managed }}
              </div>
              <div
                class="mt-1 text-xs text-gray-600 dark:text-gray-300 sm:text-sm"
              >
                Teams Managed
              </div>
            </div>

            <div
              class="rounded-lg bg-green-50 p-3 text-center transition-all hover:scale-105 dark:bg-green-900/20 sm:p-4"
            >
              <div
                class="text-xl font-bold text-green-600 dark:text-green-400 sm:text-2xl"
              >
                {{ teamStats.total_team_job_orders }}
              </div>
              <div
                class="mt-1 text-xs text-gray-600 dark:text-gray-300 sm:text-sm"
              >
                Team Job Orders
              </div>
            </div>

            <div
              class="col-span-1 rounded-lg bg-purple-50 p-3 text-center transition-all hover:scale-105 dark:bg-purple-900/20 sm:col-span-1 sm:p-4"
            >
              <div
                class="text-xl font-bold text-purple-600 dark:text-purple-400 sm:text-2xl"
              >
                {{ teamStats.team_success_rate }}%
              </div>
              <div
                class="mt-1 text-xs text-gray-600 dark:text-gray-300 sm:text-sm"
              >
                Success Rate
              </div>
            </div>
          </div>
        </div>

        <!-- Performance Evaluation -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-sky-900 dark:text-sky-700">
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
      </div>
    </div>
  </div>
</template>
