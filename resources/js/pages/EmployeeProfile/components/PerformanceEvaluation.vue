<script setup lang="ts">
import { Award, Calendar, FileText, User } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'

interface PerformanceStats {
  total_created: number
  monthly_created: number
  completed_jobs: number
  corrections_count?: number
  error_count?: number
  success_rate: number
  current_month: string
}

interface Employee {
  id: number
  full_name: string
}

const props = defineProps<{
  employee: Employee
  performanceStats?: PerformanceStats | null
  performanceEvaluations?: any[] | null
}>()

const selectedMonth = ref('August 2025')
const isLoading = ref(false)

const currentPerformanceData = ref<PerformanceStats | null>(null)

const months = computed(() => {
  const currentYear = new Date().getFullYear()
  const previousYear = currentYear - 1
  const monthNames = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
  ]

  const allMonths = []

  for (const month of monthNames) {
    allMonths.push(`${month} ${currentYear}`)
  }

  for (const month of monthNames) {
    allMonths.push(`${month} ${previousYear}`)
  }

  return allMonths.sort((a, b) => new Date(b).getTime() - new Date(a).getTime())
})

const computedPerformanceData = computed(() => {
  const data = currentPerformanceData.value
  if (data) {
    return {
      currentRating: data.success_rate || 0,
      totalEvaluations: data.total_created || 0,
      successfulJobs: data.completed_jobs || 0,
      completionsMade: data.monthly_created || 0,
      correctionsCount: data.corrections_count || 0,
      errorCount: data.error_count || 0,
      currentMonth: data.current_month || selectedMonth.value,
    }
  }

  return {
    currentRating: 0,
    totalEvaluations: 0,
    successfulJobs: 0,
    completionsMade: 0,
    correctionsCount: 0,
    errorCount: 0,
    currentMonth: selectedMonth.value,
  }
})

const handleMonthChange = async () => {
  isLoading.value = true
  try {
    const [monthName, year] = selectedMonth.value.split(' ')
    const monthNumber =
      new Date(Date.parse(monthName + ' 1, 2000')).getMonth() + 1
    const formattedMonth = `${year}-${monthNumber.toString().padStart(2, '0')}`

    const response = await fetch(
      `/profile/${props.employee.id}/performance-data?month=${formattedMonth}`,
      {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      },
    )

    if (response.ok) {
      const data = await response.json()

      if (data.stats) {
        currentPerformanceData.value = {
          total_created: data.stats.total_created || 0,
          monthly_created: data.stats.monthly_created || 0,
          completed_jobs: data.stats.completed_jobs || 0,
          corrections_count: data.stats.corrections_count || 0,
          error_count: data.stats.error_count || 0,
          success_rate: data.stats.success_rate || 0,
          current_month: data.stats.current_month || selectedMonth.value,
        }
      }
    }
  } catch (error) {
    // Handle error silently
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (props.performanceStats) {
    currentPerformanceData.value = { ...props.performanceStats }
    if (props.performanceStats.current_month) {
      selectedMonth.value = props.performanceStats.current_month
    }
  }
})
</script>

<template>
  <div class="relative rounded-lg p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6">
    <!-- Header -->
    <div
      class="mb-4 flex flex-col gap-4 sm:mb-6 sm:flex-row sm:items-center sm:justify-between"
    >
      <h3 class="text-lg font-semibold text-sky-900 dark:text-white">
        Performance Evaluation
      </h3>
      <div class="relative">
        <select
          v-model="selectedMonth"
          @change="handleMonthChange"
          :disabled="isLoading"
          class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2 pr-10 text-sm text-sky-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:ring-blue-400 sm:min-w-[140px]"
        >
          <option
            v-for="month in months"
            :key="month"
            :value="month"
          >
            {{ month }}
          </option>
        </select>
        <Calendar
          class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-gray-400 dark:text-gray-500"
        />
      </div>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300 sm:mb-6">
      Your Performance Evaluation for this month
    </div>

    <!-- Main Content: Responsive Layout -->
    <div
      class="mb-6 flex flex-col gap-6 lg:mb-8 lg:flex-row lg:items-start lg:gap-8"
    >
      <!-- Circular Progress - Full width on mobile, left side on desktop -->
      <div class="flex justify-center lg:flex-shrink-0">
        <div
          class="relative h-32 w-32 sm:h-40 sm:w-40 lg:mt-20 lg:h-48 lg:w-48"
        >
          <!-- Outer glow rings -->
          <div
            class="absolute inset-0 animate-pulse rounded-full bg-gradient-to-r from-green-100 to-lime-100 opacity-30 dark:from-green-900/20 dark:to-lime-900/20"
          ></div>
          <div
            class="absolute inset-2 rounded-full bg-gradient-to-r from-green-50 to-lime-50 opacity-50 dark:from-green-900/10 dark:to-lime-900/10"
          ></div>

          <!-- Main circular progress -->
          <div class="absolute inset-4 flex items-center justify-center">
            <svg
              class="h-24 w-24 -rotate-90 transform sm:h-32 sm:w-32 lg:h-40 lg:w-40"
              viewBox="0 0 120 120"
            >
              <!-- Background circle -->
              <circle
                cx="60"
                cy="60"
                r="50"
                stroke="#e5e7eb"
                class="dark:stroke-gray-700"
                stroke-width="8"
                fill="none"
              />

              <!-- Progress circle -->
              <circle
                cx="60"
                cy="60"
                r="50"
                stroke="url(#progressGradient)"
                stroke-width="8"
                fill="none"
                :stroke-dasharray="`${computedPerformanceData.currentRating * 3.14}, 314`"
                stroke-linecap="round"
                class="drop-shadow-sm transition-all duration-1000 ease-out"
                style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.3))"
              />

              <!-- Gradient definition -->
              <defs>
                <linearGradient
                  id="progressGradient"
                  x1="0%"
                  y1="0%"
                  x2="100%"
                  y2="0%"
                >
                  <stop
                    offset="0%"
                    style="stop-color: #84cc16; stop-opacity: 1"
                  />
                  <stop
                    offset="100%"
                    style="stop-color: #22c55e; stop-opacity: 1"
                  />
                </linearGradient>
              </defs>
            </svg>

            <!-- Center percentage -->
            <div
              class="absolute inset-0 flex flex-col items-center justify-center"
            >
              <span
                class="bg-gradient-to-r from-green-600 to-lime-600 bg-clip-text text-2xl font-bold text-transparent dark:from-green-400 dark:to-lime-400 sm:text-3xl lg:text-4xl"
              >
                {{ computedPerformanceData.currentRating }}%
              </span>
              <span
                class="mt-1 text-xs text-gray-500 dark:text-gray-400 sm:text-sm"
                >out of 100</span
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Stats - Stack on mobile, side by side on desktop -->
      <div class="flex-1 space-y-4 sm:space-y-6">
        <!-- Job Orders Created -->
        <div
          class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-3 dark:border-blue-900/30 dark:bg-blue-900/20 sm:gap-4 sm:p-4"
        >
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/40 sm:h-12 sm:w-12"
          >
            <User
              class="h-5 w-5 text-blue-600 dark:text-blue-400 sm:h-6 sm:w-6"
            />
          </div>
          <div class="min-w-0 flex-1">
            <div
              class="text-xl font-bold text-sky-900 dark:text-white sm:text-2xl"
            >
              {{ computedPerformanceData.totalEvaluations }}
            </div>
            <div
              class="truncate text-sm font-medium text-gray-700 dark:text-gray-200"
            >
              Job Orders Created
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              as of {{ selectedMonth }}
            </div>
          </div>
        </div>

        <!-- Successful Job Orders -->
        <div
          class="flex items-center gap-3 rounded-xl border border-green-100 bg-green-50 p-3 dark:border-green-900/30 dark:bg-green-900/20 sm:gap-4 sm:p-4"
        >
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/40 sm:h-12 sm:w-12"
          >
            <FileText
              class="h-5 w-5 text-green-600 dark:text-green-400 sm:h-6 sm:w-6"
            />
          </div>
          <div class="min-w-0 flex-1">
            <div
              class="text-xl font-bold text-sky-900 dark:text-white sm:text-2xl"
            >
              {{ computedPerformanceData.successfulJobs }}
            </div>
            <div
              class="truncate text-sm font-medium text-gray-700 dark:text-gray-200"
            >
              Successful Job Orders
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              as of {{ selectedMonth }}
            </div>
          </div>
        </div>

        <!-- Corrections Made -->
        <div
          class="flex items-center gap-3 rounded-xl border border-orange-100 bg-orange-50 p-3 dark:border-orange-900/30 dark:bg-orange-900/20 sm:gap-4 sm:p-4"
        >
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/40 sm:h-12 sm:w-12"
          >
            <Award
              class="h-5 w-5 text-orange-600 dark:text-orange-400 sm:h-6 sm:w-6"
            />
          </div>
          <div class="min-w-0 flex-1">
            <div
              class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl"
            >
              {{ computedPerformanceData.correctionsCount }}
            </div>
            <div
              class="truncate text-sm font-medium text-gray-700 dark:text-gray-200"
            >
              Corrections Made
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              as of {{ selectedMonth }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section: Error Tracking -->
    <div class="border-t border-gray-200 pt-4 dark:border-gray-700 sm:pt-6">
      <div class="mb-4">
        <h4
          class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white sm:text-base"
        >
          <div class="h-2 w-2 flex-shrink-0 rounded-full bg-red-500"></div>
          Error Analysis
        </h4>
        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 sm:text-sm">
          Tracking job order errors for quality improvement
        </p>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
        <!-- Total Errors -->
        <div
          class="flex items-center gap-3 rounded-xl border border-red-100 bg-gradient-to-r from-red-50 to-pink-50 p-4 shadow-sm dark:border-red-900/30 dark:from-red-900/20 dark:to-pink-900/20 sm:gap-4 sm:p-5"
        >
          <div
            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200 shadow-inner dark:from-red-900/40 dark:to-red-800/40 sm:h-14 sm:w-14"
          >
            <svg
              class="h-6 w-6 text-red-600 dark:text-red-400 sm:h-7 sm:w-7"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
              ></path>
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div
              class="text-2xl font-bold text-red-700 dark:text-red-300 sm:text-3xl"
            >
              {{ computedPerformanceData.errorCount }}
            </div>
            <div
              class="truncate text-sm font-medium text-red-700 dark:text-red-300"
            >
              Total Job Order Errors
            </div>
            <div class="mt-1 text-xs text-red-600 dark:text-red-400">
              Cumulative as of {{ selectedMonth }}
            </div>
          </div>
        </div>

        <!-- Error Rate -->
        <div
          class="flex items-center gap-3 rounded-xl border border-amber-100 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm dark:border-amber-900/30 dark:from-amber-900/20 dark:to-orange-900/20 sm:gap-4 sm:p-5"
        >
          <div
            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-orange-200 shadow-inner dark:from-amber-900/40 dark:to-orange-800/40 sm:h-14 sm:w-14"
          >
            <svg
              class="h-6 w-6 text-amber-600 dark:text-amber-400 sm:h-7 sm:w-7"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              ></path>
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div
              class="text-2xl font-bold text-amber-700 dark:text-amber-300 sm:text-3xl"
            >
              {{
                computedPerformanceData.totalEvaluations > 0
                  ? Math.round(
                      (computedPerformanceData.errorCount /
                        computedPerformanceData.totalEvaluations) *
                        100,
                    )
                  : 0
              }}%
            </div>
            <div
              class="truncate text-sm font-medium text-amber-700 dark:text-amber-300"
            >
              Error Rate
            </div>
            <div
              class="mt-1 truncate text-xs text-amber-600 dark:text-amber-400"
            >
              {{ computedPerformanceData.errorCount }} errors /
              {{ computedPerformanceData.totalEvaluations }} total orders
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div
      v-if="isLoading"
      class="absolute inset-0 flex items-center justify-center rounded-lg bg-white bg-opacity-90 backdrop-blur-sm dark:bg-gray-900 dark:bg-opacity-90"
    >
      <div class="flex items-center gap-3">
        <div
          class="h-5 w-5 animate-spin rounded-full border-2 border-blue-600 border-t-transparent dark:border-blue-400"
        ></div>
        <div class="text-sm text-gray-600 dark:text-gray-300">
          Loading performance data...
        </div>
      </div>
    </div>
  </div>
</template>
