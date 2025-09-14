<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

interface JobOrder {
    id: number | string
    ticket?: string
    status?: string
    client?: string
    serviceable_type?: string
    team_leader?: string
    safety_officer?: string
    service_area?: string
    created_at?: string
    updated_at?: string
}

const props = defineProps<{
    jobOrders: JobOrder[]
    positionName: string
}>()

console.log(props.jobOrders)

const searchQuery = ref('')

const filteredJobOrders = computed(() => {
    if (!searchQuery.value) return props.jobOrders

    const query = searchQuery.value.toLowerCase()
    return props.jobOrders.filter(job =>
        job.id.toString().toLowerCase().includes(query) ||
        (job.ticket?.toLowerCase().includes(query)) ||
        (job.team_leader?.toLowerCase().includes(query)) ||
        (job.safety_officer?.toLowerCase().includes(query)) ||
        (job.service_area?.toLowerCase().includes(query)) ||
        (job.client?.toLowerCase().includes(query))
    )
})

const formatDate = (dateString: string | undefined) => {
    if (!dateString) return 'N/A'
    try {
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        })
    } catch {
        return dateString
    }
}

const getJobOrderId = (job: JobOrder) => {
    return job.ticket || job.id
}

const viewJobOrder = (job: JobOrder) => {
    const routeParam = job.ticket || job.id
    router.visit(route('job_order.index', { jobOrder: routeParam }))
}
</script>

<template>
    <div class="dark:bg-gray-900 rounded-lg shadow-sm p-3 sm:p-4 lg:p-6">
        <!-- Header with title and search -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
            <h3 class="text-lg font-semibold text-sky-900 dark:text-white">Job Orders</h3>
            <div class="relative">
                <Search class="absolute left-3 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500" />
                <input v-model="searchQuery" type="text" placeholder="Search"
                    class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sky-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 w-full sm:w-64" />
            </div>
        </div>

        <!-- Job orders container -->
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="max-h-[400px] sm:max-h-[500px] lg:max-h-[643px] overflow-y-auto">
                <div class="w-full">
                    <div class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="job in filteredJobOrders" :key="job.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors p-3 sm:p-4 lg:p-6">

                            <!-- Mobile Layout (default) -->
                            <div class="flex flex-col space-y-3 lg:hidden">
                                <!-- Job ID and Date -->
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-semibold text-sky-900 dark:text-white text-base">
                                            {{ getJobOrderId(job) }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ formatDate(job.created_at) }}
                                        </div>
                                    </div>
                                    <button @click="viewJobOrder(job)"
                                        class="text-blue-600 dark:text-gray-400 hover:text-blue-800 dark:hover:text-gray-300 text-sm font-medium transition-colors whitespace-nowrap">
                                        View Job Order
                                    </button>
                                </div>

                                <!-- Client and Location -->
                                <div class="space-y-1">
                                    <div class="text-sm text-gray-700 dark:text-gray-200">
                                        {{ job.client || 'Rose Mary Corp.' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ job.service_area || 'Platero, Manila' }}
                                    </div>
                                </div>

                                <!-- Team Lead -->
                                <div class="text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Team Lead:</span>
                                    <span class="ml-2 text-sky-900 dark:text-white">{{ job.team_leader || 'Blessed'
                                        }}</span>
                                </div>
                            </div>

                            <!-- Desktop Layout (lg and above) -->
                            <div class="hidden lg:flex lg:items-center lg:justify-between">
                                <!-- Left section: Basic info -->
                                <div class="flex-1">
                                    <div class="font-semibold text-sky-900 dark:text-white text-base mb-1">
                                        {{ getJobOrderId(job) }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                                        {{ formatDate(job.created_at) }}
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-200">
                                        {{ job.client || 'Rose Mary Corp.' }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ job.service_area || 'Platero, Manila' }}
                                    </div>
                                </div>

                                <!-- Middle section: Team info -->
                                <div class="flex-1 px-4">
                                    <div class="text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Team Lead</span>
                                            <span class="ml-2 text-sky-900 dark:text-white">{{ job.team_leader ||
                                                'Blessed' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right section: Actions -->
                                <div class="flex flex-col space-y-2">
                                    <button @click="viewJobOrder(job)"
                                        class="text-blue-600 dark:text-gray-400 hover:text-blue-800 dark:hover:text-gray-300 text-sm font-medium transition-colors text-right">
                                        View Job Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredJobOrders.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div v-if="searchQuery">
                    No job orders found matching "{{ searchQuery }}"
                </div>
                <div v-else>
                    No job orders created yet.
                </div>
            </div>
        </div>
    </div>
</template>