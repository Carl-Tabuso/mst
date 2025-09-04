<script setup lang="ts">
import { ref, computed } from 'vue'
import { Search } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

interface JobOrder {
    id: number | string
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

const searchQuery = ref('')

const filteredJobOrders = computed(() => {
    if (!searchQuery.value) return props.jobOrders

    const query = searchQuery.value.toLowerCase()
    return props.jobOrders.filter(job =>
        job.id.toString().toLowerCase().includes(query) ||
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
    if (typeof job.id === 'number') {
        return `JO-${job.id.toString().padStart(8, '0')}`
    }
    return job.id.toString().startsWith('JO-') ? job.id : `JO-${job.id}`
}

const viewJobOrder = (jobId: number | string) => {
    router.visit(route('job_order.index', { jobOrder: jobId }))
}

// const viewEvaluation = (jobId: number | string) => {
//     router.visit(route('employee.ratings.view', jobId))
// }

console.log(props.jobOrders.map(j => [j.team_leader, j.safety_officer]))
</script>

<template>
    <div class="dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-sky-900 dark:text-white">Job Orders</h3>
            <div class="relative">
                <Search class="absolute left-3 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500" />
                <input v-model="searchQuery" type="text" placeholder="Search"
                    class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sky-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 w-64" />
            </div>
        </div>

        <!-- Table without headers -->
        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="max-h-96 overflow-y-auto">
                <div class="w-full">
                    <div class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <div v-for="job in filteredJobOrders" :key="job.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors p-6 flex items-center justify-between">
                            <!-- Left section: Job Order Info -->
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

                            <div class="flex-1 px-4">
                                <div class="text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Team Lead</span>
                                        <span class="ml-2 text-sky-900 dark:text-white">{{ job.team_leader || 'Blessed'
                                            }}</span>
                                    </div>
                                    <!-- <div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Safety Officer</span>
                                        <span class="ml-2 text-sky-900 dark:text-white">{{ job.safety_officer ||
                                            'Regina Garcia' }}</span>
                                    </div> -->
                                </div>
                            </div>

                            <!-- Right section: Actions -->
                            <div class="flex flex-col space-y-2">
                                <button @click="viewJobOrder(job.id)"
                                    class="text-blue-600 dark:text-gray-400 hover:text-blue-800 dark:hover:text-gray-300 text-sm font-medium transition-colors text-right">
                                    View Job Order
                                </button>
                                <!-- <button @click="viewEvaluation(job.id)"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors text-right">
                                    View Evaluation
                                </button> -->
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