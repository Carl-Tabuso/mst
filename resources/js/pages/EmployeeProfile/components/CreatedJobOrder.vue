<script setup lang="ts">
import { computed } from 'vue';

interface CreatedJobOrder {
  id: number;
  status: string;
  client?: string | null;
  serviceable_type?: string | null;
}

const props = defineProps<{
  createdJobOrders: { total: number; by_status: Record<string, number> } | null;
  createdJobOrdersList?: (CreatedJobOrder | null)[];
  positionName: string;
  formatStatus: (status: string) => string;
  getStatusColor: (status: string) => string;
}>();

const hasData = computed(() => !!props.createdJobOrders && props.createdJobOrders.total > 0);

const safeJobOrdersList = computed(() =>
  (props.createdJobOrdersList ?? [])
    .filter((job): job is CreatedJobOrder => job !== null)
    .map(job => ({
      ...job,
      client: job.client ?? 'N/A',
      serviceable_type: job.serviceable_type ?? 'N/A',
      status: job.status ?? 'Unknown',
    }))
);
</script>

<template>
  <div>
    <div v-if="hasData" class="mb-6">
      <h2 class="text-lg font-semibold mb-3">Job Order Statistics</h2>

      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600 mb-4">
          {{ createdJobOrders?.total }} Total Job Orders Created
        </div>

        <table class="w-full table-auto border-collapse">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left px-3 py-2 border-b">Job Order ID</th>
              <th class="text-left px-3 py-2 border-b">Serviceable Type</th>
              <th class="text-left px-3 py-2 border-b">Client</th>
              <th class="text-left px-3 py-2 border-b">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="job in safeJobOrdersList" :key="job.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-3 py-2">{{ job.id }}</td>
              <td class="px 3-py-2">{{ job.serviceable_type }}</td>
              <td class="px-3 py-2">{{ job.client }}</td>
              <td class="px-3 py-2">
                <span :class="['inline-block rounded px-2 py-1 text-sm font-medium', getStatusColor(job.status)]">
                  {{ formatStatus(job.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-else class="text-gray-400 italic text-sm mt-4">
      No job orders created.
    </div>
  </div>
</template>
