<script setup lang="ts">
defineProps<{
  jobOrderStats: { total: number; by_status: Record<string, number> } | null
  assignedJobOrders: Array<{ id: number; status?: string }>
  positionName: string
  formatStatus: (status: string) => string
  getStatusColor: (status: string) => string
}>()
</script>

<template>
  <div>
    <div
      v-if="jobOrderStats"
      class="mb-6"
    >
      <h2 class="mb-3 text-lg font-semibold">Job Order Statistics</h2>
      <div class="rounded-lg bg-gray-50 p-4">
        <div class="mb-2 text-2xl font-bold text-blue-600">
          {{ jobOrderStats.total }} Total Job Orders
        </div>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
          <div
            v-for="(count, status) in jobOrderStats.by_status"
            :key="status"
            class="rounded-lg p-3 text-center"
            :class="getStatusColor(status)"
          >
            <div class="text-lg font-semibold">{{ count }}</div>
            <div class="text-sm">{{ formatStatus(status) }}</div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="assignedJobOrders.length">
      <h2 class="mb-3 text-lg font-semibold">Assigned Job Orders</h2>
      <div class="space-y-2">
        <div
          v-for="order in assignedJobOrders"
          :key="order.id"
          class="flex items-center justify-between rounded-lg border p-3 hover:bg-gray-50"
        >
          <div class="font-medium">Job Order #{{ order.id }}</div>
          <div
            v-if="order.status"
            class="rounded-full px-3 py-1 text-sm font-medium"
            :class="getStatusColor(order.status)"
          >
            {{ formatStatus(order.status) }}
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="!assignedJobOrders.length"
      class="text-gray-400"
    >
      No job orders assigned.
    </div>
  </div>
</template>
