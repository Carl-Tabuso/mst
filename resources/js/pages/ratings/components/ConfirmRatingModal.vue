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
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4">
    <div
      class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-lg bg-white p-4 shadow-lg dark:bg-gray-800 sm:p-6 md:max-w-2xl">
      <button
        class="absolute right-3 top-3 text-xl text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
        @click="close">
        &times;
      </button>

      <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100 sm:text-xl">Confirm Ratings</h2>

      <p class="mb-4 text-sm text-gray-700 dark:text-gray-300 sm:text-base">
        Review the evaluation details and ratings carefully. To confirm that you
        have reviewed and finalized your evaluation, type
        <span class="font-bold">CONFIRM</span> in the field below. Once
        submitted, the evaluation can no longer be edited.
      </p>

      <div class="mb-4 max-h-64 overflow-auto rounded border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm sm:text-base">
          <thead class="sticky top-0 bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="px-3 py-2 text-left text-gray-900 dark:text-gray-100">Employee</th>
              <th class="px-3 py-2 text-left text-gray-900 dark:text-gray-100">Position</th>
              <th class="px-3 py-2 text-left text-gray-900 dark:text-gray-100">Rating</th>
              <th class="px-3 py-2 text-left text-gray-900 dark:text-gray-100">Remarks</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800">
            <tr v-for="row in summary" :key="row.id" class="border-t border-gray-100 dark:border-gray-700">
              <td class="px-3 py-1 text-gray-900 dark:text-gray-100">{{ row.name }}</td>
              <td class="px-3 py-1 text-gray-900 dark:text-gray-100">{{ row.position }}</td>
              <td class="px-3 py-1 text-gray-900 dark:text-gray-100">{{ row.rating }}/5</td>
              <td class="px-3 py-1 text-gray-900 dark:text-gray-100">{{ row.description }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex w-full justify-center">
        <div class="flex w-full max-w-xl flex-col items-center gap-4 sm:flex-row sm:gap-8">
          <input v-model="confirmText" type="text" placeholder="Type CONFIRM to proceed"
            class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-400 sm:w-1/3 sm:gap-4 sm:text-base"
            @keyup.enter="tryConfirm" />

          <button @click="close"
            class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 sm:w-1/3 sm:text-base">
            Edit Evaluation
          </button>

          <button :disabled="!canConfirm" @click="tryConfirm"
            class="w-full rounded-md px-4 py-2 text-sm font-semibold text-white transition sm:w-1/3 sm:text-base"
            :class="canConfirm
                ? 'bg-blue-900 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700'
                : 'cursor-not-allowed bg-gray-300 dark:bg-gray-600'
              ">
            Confirm & Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>