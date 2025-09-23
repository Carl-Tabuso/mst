<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Search } from 'lucide-vue-next'
import { computed, ref } from 'vue'

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

// console.log(props.jobOrders)

const searchQuery = ref('')

const filteredJobOrders = computed(() => {
  if (!searchQuery.value) return props.jobOrders

  const query = searchQuery.value.toLowerCase()
  return props.jobOrders.filter(
    (job) =>
      job.id.toString().toLowerCase().includes(query) ||
      job.ticket?.toLowerCase().includes(query) ||
      job.team_leader?.toLowerCase().includes(query) ||
      job.safety_officer?.toLowerCase().includes(query) ||
      job.service_area?.toLowerCase().includes(query) ||
      job.client?.toLowerCase().includes(query),
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
      hour12: true,
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
  <div class="rounded-lg p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6">
    <!-- Header with title and search -->
    <div
      class="mb-4 flex flex-col gap-4 sm:mb-6 sm:flex-row sm:items-center sm:justify-between"
    >
      <h3 class="text-lg font-semibold text-sky-900 dark:text-white">
        Job Orders
      </h3>
      <div class="relative">
        <Search
          class="absolute left-3 top-2.5 h-4 w-4 text-gray-400 dark:text-gray-500"
        />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search"
          class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-4 text-sky-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-400 sm:w-64"
        />
      </div>
    </div>

    <!-- Job orders container -->
    <div
      class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
    >
      <div
        class="max-h-[400px] overflow-y-auto sm:max-h-[500px] lg:max-h-[648px]"
      >
        <div class="w-full">
          <div
            class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900"
          >
            <div
              v-for="job in filteredJobOrders"
              :key="job.id"
              class="p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50 sm:p-4 lg:p-6"
            >
              <!-- Mobile Layout (default) -->
              <div class="flex flex-col space-y-3 lg:hidden">
                <!-- Job ID and Date -->
                <div class="flex items-start justify-between">
                  <div>
                    <div
                      class="text-base font-semibold text-sky-900 dark:text-white"
                    >
                      {{ getJobOrderId(job) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                      {{ formatDate(job.created_at) }}
                    </div>
                  </div>
                  <button
                    @click="viewJobOrder(job)"
                    class="whitespace-nowrap text-sm font-medium text-blue-600 transition-colors hover:text-blue-800 dark:text-gray-400 dark:hover:text-gray-300"
                  >
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
                  <span class="font-medium text-gray-700 dark:text-gray-300"
                    >Team Lead:</span
                  >
                  <span class="ml-2 text-sky-900 dark:text-white">{{
                    job.team_leader || 'Blessed'
                  }}</span>
                </div>
              </div>

              <!-- Desktop Layout (lg and above) -->
              <div class="hidden lg:flex lg:items-center lg:justify-between">
                <!-- Left section: Basic info -->
                <div class="flex-1">
                  <div
                    class="mb-1 text-base font-semibold text-sky-900 dark:text-white"
                  >
                    {{ getJobOrderId(job) }}
                  </div>
                  <div class="mb-1 text-sm text-gray-600 dark:text-gray-300">
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
                      <span class="font-medium text-gray-700 dark:text-gray-300"
                        >Team Lead</span
                      >
                      <span class="ml-2 text-sky-900 dark:text-white">{{
                        job.team_leader || 'Blessed'
                      }}</span>
                    </div>
                  </div>
                </div>

                <!-- Right section: Actions -->
                <div class="flex flex-col space-y-2">
                  <button
                    @click="viewJobOrder(job)"
                    class="text-right text-sm font-medium text-blue-600 transition-colors hover:text-blue-800 dark:text-gray-400 dark:hover:text-gray-300"
                  >
                    View Job Order
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="filteredJobOrders.length === 0"
        class="py-8 text-center text-gray-500 dark:text-gray-400"
      >
        <div v-if="searchQuery">
          No job orders found matching "{{ searchQuery }}"
        </div>
        <div v-else>No job orders created yet.</div>
      </div>
    </div>
  </div>
</template>
