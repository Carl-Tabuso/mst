<template>
  <div class="flex items-center justify-between">
    <p
      v-if="selectedCount > 0"
      class="text-sm text-gray-600"
    >
      {{ selectedCount }} selected
    </p>
    <div class="flex gap-2">
      <button
        @click="emit('restore')"
        class="rounded-md bg-green-600 px-4 py-2 text-white shadow hover:bg-green-700"
        :disabled="selectedCount === 0"
      >
        Restore
      </button>
      <button
        @click="emit('force-delete')"
        class="rounded-md bg-red-600 px-4 py-2 text-white shadow hover:bg-red-700"
        :disabled="selectedCount === 0"
      >
        Delete Permanently
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ selectedCount: number }>()
const emit = defineEmits(['restore', 'force-delete'])
</script>
