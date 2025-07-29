<template>
  <div class="flex gap-4">
    <input
      v-model="input"
      @keyup.enter="emit('search', input)"
      type="text"
      class="w-full max-w-md rounded-md border px-4 py-2 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
      placeholder="Search client/contact..."
    />
    <button
      @click="emit('search', input)"
      class="rounded-md bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700"
    >
      Search
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue'

const props = defineProps<{ search: string }>()
const emit = defineEmits(['search'])

const input = ref('')
watchEffect(() => {
  input.value = props.search
})
</script>
