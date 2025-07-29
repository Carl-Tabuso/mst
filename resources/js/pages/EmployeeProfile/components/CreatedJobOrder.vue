<script setup lang="ts">
import { computed } from 'vue'

interface CreatedJobOrder {
  id: number
  status: string
  client?: string | null
  serviceable_type?: string | null
}

const props = defineProps<{
  createdJobOrders: { total: number; by_status: Record<string, number> } | null
  createdJobOrdersList?: (CreatedJobOrder | null)[]
  positionName: string
  formatStatus: (status: string) => string
  getStatusColor: (status: string) => string
}>()

const hasData = computed(
  () => !!props.createdJobOrders && props.createdJobOrders.total > 0,
)

const safeJobOrdersList = computed(() =>
  (props.createdJobOrdersList ?? [])
    .filter((job): job is CreatedJobOrder => job !== null)
    .map((job) => ({
      ...job,
      client: job.client ?? 'N/A',
      serviceable_type: job.serviceable_type ?? 'N/A',
      status: job.status ?? 'Unknown',
    })),
)
</script>

<template>
  <div>
    <div
      v-if="hasData"
      class="mb-6"
    >
      <h2 class="mb-3 text-lg font-semibold">Job Order Statistics</h2>

      <div class="rounded-lg bg-gray-50 p-4">
        <div class="mb-4 text-2xl font-bold text-blue-600">
          {{ createdJobOrders?.total }} Total Job Orders Created
        </div>

        <table class="w-full table-auto border-collapse">
          <thead class="bg-gray-100">
            <tr>
              <th class="border-b px-3 py-2 text-left">Job Order ID</th>
              <th class="border-b px-3 py-2 text-left">Serviceable Type</th>
              <th class="border-b px-3 py-2 text-left">Client</th>
              <th class="border-b px-3 py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="job in safeJobOrdersList"
              :key="job.id"
              class="transition-colors hover:bg-gray-50"
            >
              <td class="px-3 py-2">{{ job.id }}</td>
              <td class="px 3-py-2">{{ job.serviceable_type }}</td>
              <td class="px-3 py-2">{{ job.client }}</td>
              <td class="px-3 py-2">
                <span
                  :class="[
                    'inline-block rounded px-2 py-1 text-sm font-medium',
                    getStatusColor(job.status),
                  ]"
                >
                  {{ formatStatus(job.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-else
      class="mt-4 text-sm italic text-gray-400"
    >
      No job orders created.
    </div>
  </div>
</template>
