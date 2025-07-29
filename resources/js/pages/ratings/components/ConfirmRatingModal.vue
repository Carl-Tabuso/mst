<script setup lang="ts">
import { computed, ref, watch } from 'vue'

const props = defineProps<{
  show: boolean
  summary: Array<{
    id: number
    name: string
    position: string
    rating: number
    description: string
  }>
}>()
const emit = defineEmits(['close', 'confirm'])

const confirmText = ref('')

const canConfirm = computed(() =>
  ['confirm', 'CONFIRM', 'Confirm'].includes(confirmText.value.trim()),
)

function tryConfirm() {
  if (canConfirm.value) {
    emit('confirm')
    confirmText.value = ''
  }
}
function close() {
  emit('close')
  confirmText.value = ''
}
watch(
  () => props.show,
  (val) => {
    if (!val) confirmText.value = ''
  },
)
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4"
  >
    <div
      class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg bg-white p-4 shadow-lg sm:p-6 md:max-w-2xl"
    >
      <button
        class="absolute right-3 top-3 text-xl text-gray-400 hover:text-gray-600"
        @click="close"
      >
        &times;
      </button>

      <h2 class="mb-4 text-lg font-semibold sm:text-xl">Confirm Ratings</h2>

      <p class="mb-4 text-sm text-gray-700 sm:text-base">
        Review the evaluation details and ratings carefully. To confirm that you
        have reviewed and finalized your evaluation, type
        <span class="font-bold">CONFIRM</span> in the field below. Once
        submitted, the evaluation can no longer be edited.
      </p>

      <div class="mb-4 max-h-64 overflow-auto rounded border">
        <table class="w-full text-sm sm:text-base">
          <thead class="sticky top-0 bg-gray-100">
            <tr>
              <th class="px-3 py-2 text-left">Employee</th>
              <th class="px-3 py-2 text-left">Position</th>
              <th class="px-3 py-2 text-left">Rating</th>
              <th class="px-3 py-2 text-left">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="row in summary"
              :key="row.id"
              class="border-t"
            >
              <td class="px-3 py-1">{{ row.name }}</td>
              <td class="px-3 py-1">{{ row.position }}</td>
              <td class="px-3 py-1">{{ row.rating }}/5</td>
              <td class="px-3 py-1">{{ row.description }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex w-full justify-center">
        <div
          class="flex w-full max-w-xl flex-col items-center gap-4 sm:flex-row sm:gap-8"
        >
          <!-- Confirmation Input -->
          <input
            v-model="confirmText"
            type="text"
            placeholder="Type CONFIRM to proceed"
            class="w-full rounded-md border border-gray-300 px-4 py-2 text-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-1/3 sm:gap-4 sm:text-base"
            @keyup.enter="tryConfirm"
          />

          <!-- Edit Evaluation Button -->
          <button
            @click="close"
            class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 sm:w-1/3 sm:text-base"
          >
            Edit Evaluation
          </button>

          <!-- Confirm Button -->
          <button
            :disabled="!canConfirm"
            @click="tryConfirm"
            class="w-full rounded-md px-4 py-2 text-sm font-semibold text-white transition sm:w-1/3 sm:text-base"
            :class="
              canConfirm
                ? 'bg-blue-900 hover:bg-blue-800'
                : 'cursor-not-allowed bg-gray-300'
            "
          >
            Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
