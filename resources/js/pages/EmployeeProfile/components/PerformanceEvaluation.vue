<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Calendar, User, FileText, Award } from 'lucide-vue-next'

interface PerformanceStats {
    total_created: number
    monthly_created: number
    completed_jobs: number
    corrections_count?: number
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

// This will hold the current performance data (either from props initially or from API calls)
const currentPerformanceData = ref<PerformanceStats | null>(null)

// Generate months for the dropdown (current year and previous year)
const months = computed(() => {
    const currentYear = new Date().getFullYear()
    const previousYear = currentYear - 1
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ]

    const allMonths = []

    // Add current year months
    for (const month of monthNames) {
        allMonths.push(`${month} ${currentYear}`)
    }

    // Add previous year months
    for (const month of monthNames) {
        allMonths.push(`${month} ${previousYear}`)
    }

    return allMonths.sort((a, b) => new Date(b).getTime() - new Date(a).getTime())
})

// Compute performance data from current state
const computedPerformanceData = computed(() => {
    const data = currentPerformanceData.value
    if (data) {
        return {
            currentRating: data.success_rate || 0,
            totalEvaluations: data.total_created || 0,
            successfulJobs: data.completed_jobs || 0,
            completionsMade: data.monthly_created || 0,
            correctionsCount: data.corrections_count || 0,
            currentMonth: data.current_month || selectedMonth.value
        }
    }

    // Fallback to default values
    return {
        currentRating: 0,
        totalEvaluations: 0,
        successfulJobs: 0,
        completionsMade: 0,
        correctionsCount: 0,
        currentMonth: selectedMonth.value
    }
})

const handleMonthChange = async () => {
    isLoading.value = true
    try {
        // Convert "August 2025" to "2025-08" format for API
        const [monthName, year] = selectedMonth.value.split(' ')
        const monthNumber = new Date(Date.parse(monthName + " 1, 2000")).getMonth() + 1
        const formattedMonth = `${year}-${monthNumber.toString().padStart(2, '0')}`

        console.log('Fetching data for month:', formattedMonth)

        // Make API call to get performance data for selected month
        const response = await fetch(`/profile/${props.employee.id}/performance-data?month=${formattedMonth}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        if (response.ok) {
            const data = await response.json()
            console.log('Received API response:', data)

            // Update current performance data with the response
            if (data.stats) {
                currentPerformanceData.value = {
                    total_created: data.stats.total_created || 0,
                    monthly_created: data.stats.monthly_created || 0,
                    completed_jobs: data.stats.completed_jobs || 0,
                    corrections_count: data.stats.corrections_count || 0,
                    success_rate: data.stats.success_rate || 0,
                    current_month: data.stats.current_month || selectedMonth.value
                }
                console.log('Updated performance data:', currentPerformanceData.value)
            }
        } else {
            console.error('API response not ok:', response.status, response.statusText)
        }
    } catch (error) {
        console.error('Error fetching performance data:', error)
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    // Initialize with props data if available
    if (props.performanceStats) {
        currentPerformanceData.value = { ...props.performanceStats }
        if (props.performanceStats.current_month) {
            selectedMonth.value = props.performanceStats.current_month
        }
    }
})
</script>

<template>
    <div class= "dark:bg-gray-800 rounded-lg shadow-sm p-6 relative">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-sky-900 dark:text-white">Performance Evaluation</h3>
            <div class="relative">
                <select v-model="selectedMonth" @change="handleMonthChange" :disabled="isLoading"
                    class="appearance-none bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-sky-900 dark:text-white rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 min-w-[140px]">
                    <option v-for="month in months" :key="month" :value="month">
                        {{ month }}
                    </option>
                </select>
                <Calendar
                    class="absolute right-3 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none" />
            </div>
        </div>

        <div class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            Your Performance Evaluation for this month
        </div>

        <!-- Main Content: Circular Progress + Stats Side by Side -->
        <div class="flex items-start gap-8">
            <!-- Left Side: Circular Progress -->
            <div class="flex-shrink-0">
                <div class="relative w-48 h-48 mt-20">
                    <!-- Outer glow rings -->
                    <div
                        class="absolute inset-0 rounded-full bg-gradient-to-r from-green-100 to-lime-100 dark:from-green-900/20 dark:to-lime-900/20 opacity-30 animate-pulse">
                    </div>
                    <div
                        class="absolute inset-2 rounded-full bg-gradient-to-r from-green-50 to-lime-50 dark:from-green-900/10 dark:to-lime-900/10 opacity-50">
                    </div>

                    <!-- Main circular progress -->
                    <div class="absolute inset-4 flex items-center justify-center">
                        <svg class="w-40 h-40 transform -rotate-90" viewBox="0 0 120 120">
                            <!-- Background circle -->
                            <circle cx="60" cy="60" r="50" stroke="#e5e7eb" class="dark:stroke-gray-700"
                                stroke-width="8" fill="none" />

                            <!-- Progress circle -->
                            <circle cx="60" cy="60" r="50" stroke="url(#progressGradient)" stroke-width="8" fill="none"
                                :stroke-dasharray="`${computedPerformanceData.currentRating * 3.14}, 314`"
                                stroke-linecap="round" class="transition-all duration-1000 ease-out drop-shadow-sm"
                                style="filter: drop-shadow(0 0 8px rgba(34, 197, 94, 0.3))" />

                            <!-- Gradient definition -->
                            <defs>
                                <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#84cc16;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#22c55e;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>

                        <!-- Center percentage -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span
                                class="text-4xl font-bold bg-gradient-to-r from-green-600 to-lime-600 dark:from-green-400 dark:to-lime-400 bg-clip-text text-transparent">
                                {{ computedPerformanceData.currentRating }}%
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">out of 100</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Performance Stats -->
            <div class="flex-1 space-y-6">
                <!-- Job Orders Created -->
                <div
                    class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/30">
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <User class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-bold text-sky-900 dark:text-white">
                            {{ computedPerformanceData.totalEvaluations }}
                        </div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            Job Orders Created
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            as of {{ selectedMonth }}
                        </div>
                    </div>
                </div>

                <!-- Successful Job Orders -->
                <div
                    class="flex items-center gap-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30">
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <FileText class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-bold text-sky-900 dark:text-white">
                            {{ computedPerformanceData.successfulJobs }}
                        </div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            Successful Job Orders
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            as of {{ selectedMonth }}
                        </div>
                    </div>
                </div>

                <!-- Corrections Made -->
                <div
                    class="flex items-center gap-4 p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-900/30">
                    <div
                        class="w-12 h-12 bg-orange-100 dark:bg-orange-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                        <Award class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="flex-1">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ computedPerformanceData.correctionsCount }}
                        </div>
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            Corrections Made
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            as of {{ selectedMonth }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading state -->
        <div v-if="isLoading"
            class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 flex items-center justify-center rounded-lg backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div
                    class="w-5 h-5 border-2 border-blue-600 dark:border-blue-400 border-t-transparent rounded-full animate-spin">
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">Loading performance data...</div>
            </div>
        </div>
    </div>
</template>