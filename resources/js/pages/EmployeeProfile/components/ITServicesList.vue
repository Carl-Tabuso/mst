<script setup lang="ts">
defineProps<{
  services: Array<{ id: number; machine_type: string; model: string }>
}>()
</script>

<template>
  <div>
    <h2 class="mb-2 text-lg font-semibold">
      IT Services Handled
      <span class="text-base font-normal text-gray-500"
        >({{ services.length }})</span
      >
    </h2>
    <ul
      v-if="services.length"
      class="ml-6 list-disc"
    >
      <li
        v-for="service in services"
        :key="service.id"
      >
        {{ service.machine_type }} - {{ service.model }}
      </li>
    </ul>
    <div
      v-else
      class="text-gray-400"
    >
      No IT services handled.
    </div>
  </div>
</template>
