<template>
    <div class="flex gap-4">
        <input v-model="input" @keyup.enter="emit('search', input)" type="text"
            class="border rounded-md px-4 py-2 w-full max-w-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Search client/contact..." />
        <button @click="emit('search', input)"
            class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
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
