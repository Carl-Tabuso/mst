<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
  show: boolean,
  employee: { id: number, full_name: string, position: string } | null,
  received: Array<{
    from: string,
    from_position: string,
    scale: string,
    description: string,
    date: string,
  }>
}>();
const emit = defineEmits(['close']);

watch(() => props.show, val => {
  if (!val) emit('close');
});
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-4 sm:p-6 relative overflow-y-auto max-h-[90vh]">
      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl" @click="$emit('close')">&times;</button>
      <h2 class="text-lg sm:text-xl font-semibold mb-4">
        Rating History for {{ employee?.full_name }} <span class="text-gray-500">({{ employee?.position }})</span>
      </h2>
      <div class="overflow-auto max-h-96 mb-4 border rounded">
        <table class="w-full text-sm sm:text-base">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="py-2 px-3 text-left">From</th>
              <th class="py-2 px-3 text-left">Position</th>
              <th class="py-2 px-3 text-left">Scale</th>
              <th class="py-2 px-3 text-left">Description</th>
              <th class="py-2 px-3 text-left">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in received" :key="row.from + row.date">
              <td class="py-1 px-3">{{ row.from }}</td>
              <td class="py-1 px-3">{{ row.from_position }}</td>
              <td class="py-1 px-3">{{ row.scale }}</td>
              <td class="py-1 px-3">{{ row.description }}</td>
              <td class="py-1 px-3">{{ row.date }}</td>
            </tr>
            <tr v-if="!received.length">
              <td colspan="5" class="text-center text-gray-400">No ratings received.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>