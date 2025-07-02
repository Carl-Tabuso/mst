<script setup lang="ts">
defineProps<{
  jobOrderStats: { total: number, by_status: Record<string, number> } | null,
  assignedJobOrders: Array<{ id: number, status?: string }>,
  positionName: string,
  formatStatus: (status: string) => string,
  getStatusColor: (status: string) => string
}>();
</script>

<template>
  <div>
    <div v-if="jobOrderStats" class="mb-6">
      <h2 class="text-lg font-semibold mb-3">Job Order Statistics</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600 mb-2">
          {{ jobOrderStats.total }} Total Job Orders
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          <div
            v-for="(count, status) in jobOrderStats.by_status"
            :key="status"
            class="text-center p-3 rounded-lg"
            :class="getStatusColor(status)"
          >
            <div class="font-semibold text-lg">{{ count }}</div>
            <div class="text-sm">{{ formatStatus(status) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="assignedJobOrders.length">
      <h2 class="text-lg font-semibold mb-3">Assigned Job Orders</h2>
      <div class="space-y-2">
        <div
          v-for="order in assignedJobOrders"
          :key="order.id"
          class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50"
        >
          <div class="font-medium">Job Order #{{ order.id }}</div>
          <div
            v-if="order.status"
            class="px-3 py-1 rounded-full text-sm font-medium"
            :class="getStatusColor(order.status)"
          >
            {{ formatStatus(order.status) }}
          </div>
        </div>
      </div>
    </div>

    <div v-if="!assignedJobOrders.length" class="text-gray-400">
      No job orders assigned.
    </div>
  </div>
</template>
