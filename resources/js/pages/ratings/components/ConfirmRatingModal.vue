<script setup lang="ts">
import { ref, computed, watch } from 'vue';

const props = defineProps<{
  show: boolean,
  summary: Array<{ id: number, name: string, position: string, rating: number, description: string }>
}>();
const emit = defineEmits(['close', 'confirm']);

const confirmText = ref('');

const canConfirm = computed(() =>
  ['confirm', 'CONFIRM', 'Confirm'].includes(confirmText.value.trim())
);

function tryConfirm() {
  if (canConfirm.value) {
    emit('confirm');
    confirmText.value = '';
  }
}
function close() {
  emit('close');
  confirmText.value = '';
}
watch(() => props.show, (val) => {
  if (!val) confirmText.value = '';
});
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 px-4">
    <div
      class="bg-white rounded-lg shadow-lg w-full max-w-lg md:max-w-2xl p-4 sm:p-6 relative overflow-y-auto max-h-[90vh]">
      <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl" @click="close">&times;</button>

      <h2 class="text-lg sm:text-xl font-semibold mb-4">Confirm Ratings</h2>

      <p class="mb-4 text-sm sm:text-base text-gray-700">
        Review the evaluation details and ratings carefully. To confirm that you have reviewed and finalized your
        evaluation, type <span class="font-bold">CONFIRM</span> in the field below. Once submitted, the evaluation can
        no longer be edited.
      </p>

      <div class="overflow-auto max-h-64 mb-4 border rounded">
        <table class="w-full text-sm sm:text-base">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="py-2 px-3 text-left">Employee</th>
              <th class="py-2 px-3 text-left">Position</th>
              <th class="py-2 px-3 text-left">Rating</th>
              <th class="py-2 px-3 text-left">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in summary" :key="row.id" class="border-t">
              <td class="py-1 px-3">{{ row.name }}</td>
              <td class="py-1 px-3">{{ row.position }}</td>
              <td class="py-1 px-3">{{ row.rating }}/5</td>
              <td class="py-1 px-3">{{ row.description }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="w-full flex justify-center">
        <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-8 w-full max-w-xl">

          <!-- Confirmation Input -->
          <input v-model="confirmText" type="text" placeholder="Type CONFIRM to proceed"
            class="w-full sm:w-1/3 border border-gray-300 rounded-md sm:gap-4 px-4 py-2 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            @keyup.enter="tryConfirm" />

          <!-- Edit Evaluation Button -->
          <button @click="close"
            class="w-full sm:w-1/3 border border-gray-300 rounded-md px-4 py-2 text-sm sm:text-base text-gray-700 bg-white hover:bg-gray-100 font-semibold transition">
            Edit Evaluation
          </button>

          <!-- Confirm Button -->
          <button :disabled="!canConfirm" @click="tryConfirm"
            class="w-full sm:w-1/3 px-4 py-2 rounded-md text-sm sm:text-base font-semibold transition text-white"
            :class="canConfirm ? 'bg-blue-900 hover:bg-blue-800' : 'bg-gray-300 cursor-not-allowed'">
            Confirm & Submit
          </button>

        </div>
      </div>

    </div>
  </div>
</template>