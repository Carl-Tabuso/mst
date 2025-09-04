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
    <div class="space-y-6">
        <!-- Leadership + Rating Combined -->
        <div v-if="teamStats || averagePerformanceRating" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Team Leadership Stats -->
                <div v-if="teamStats">
                    <h3 class="text-lg font-semibold text-sky-900 dark:text-sky-700 mb-4">
                        Team Leadership Stats
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ teamStats.teams_managed }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Teams Managed</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ teamStats.total_team_job_orders }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Team Job Orders</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ teamStats.team_success_rate }}%
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Success Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Performance Evaluation -->
                <div>
                    <h3 class="text-lg font-semibold text-sky-900 dark:text-sky-700 mb-4">
                        Performance Evaluation
                    </h3>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
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
