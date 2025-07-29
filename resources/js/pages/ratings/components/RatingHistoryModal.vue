<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
  show: boolean
  employee: { id: number; full_name: string; position: string } | null
  received: Array<{
    from: string
    from_position: string
    scale: string
    description: string
    date: string
  }>
}>()
const emit = defineEmits(['close'])

watch(
  () => props.show,
  (val) => {
    if (!val) emit('close')
  },
)
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4"
  >
    <div
      class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-4 shadow-lg sm:p-6"
    >
      <button
        class="absolute right-3 top-3 text-xl text-gray-400 hover:text-gray-600"
        @click="$emit('close')"
      >
        &times;
      </button>
      <h2 class="mb-4 text-lg font-semibold sm:text-xl">
        Rating History for {{ employee?.full_name }}
        <span class="text-gray-500">({{ employee?.position }})</span>
      </h2>
      <div class="mb-4 max-h-96 overflow-auto rounded border">
        <table class="w-full text-sm sm:text-base">
          <thead class="sticky top-0 bg-gray-100">
            <tr>
              <th class="px-3 py-2 text-left">From</th>
              <th class="px-3 py-2 text-left">Position</th>
              <th class="px-3 py-2 text-left">Scale</th>
              <th class="px-3 py-2 text-left">Description</th>
              <th class="px-3 py-2 text-left">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="row in received"
              :key="row.from + row.date"
            >
              <td class="px-3 py-1">{{ row.from }}</td>
              <td class="px-3 py-1">{{ row.from_position }}</td>
              <td class="px-3 py-1">{{ row.scale }}</td>
              <td class="px-3 py-1">{{ row.description }}</td>
              <td class="px-3 py-1">{{ row.date }}</td>
            </tr>
            <tr v-if="!received.length">
              <td
                colspan="5"
                class="text-center text-gray-400"
              >
                No ratings received.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
