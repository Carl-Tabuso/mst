<script setup lang="ts">
defineProps<{ services: Array<{ id: number, machine_type: string, model: string }> }>();
</script>

<template>
  <div>
    <h2 class="text-lg font-semibold mb-2">
      IT Services Handled
      <span class="text-base font-normal text-gray-500">({{ services.length }})</span>
    </h2>
    <ul v-if="services.length" class="list-disc ml-6">
      <li v-for="service in services" :key="service.id">
        {{ service.machine_type }} - {{ service.model }}
      </li>
    </ul>
    <div v-else class="text-gray-400">No IT services handled.</div>
  </div>
</template>
