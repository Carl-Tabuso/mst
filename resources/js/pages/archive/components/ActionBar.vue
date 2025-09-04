<template>
  <div v-if="selectedCount > 0" class="flex items-center gap-2">
    <span class="text-sm text-gray-600 dark:text-gray-400">
      {{ selectedCount }} selected
    </span>

    <!-- Restore Button -->
    <button @click="showRestoreConfirmation" :disabled="isLoading"
      class="h-9 px-3 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 disabled:opacity-50 flex items-center gap-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
      <RotateCcw class="h-4 w-4" />
      Restore
    </button>

    <!-- Delete Button -->
    <button @click="showDeleteConfirmation" :disabled="isLoading"
      class="h-9 px-3 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 disabled:opacity-50 flex items-center gap-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
      <Trash2 class="h-4 w-4" />
      Delete
    </button>
  </div>

  <!-- Confirmation Modals -->
  <ConfirmationModal :is-open="showRestoreModal" type="restore" :item-count="selectedCount" :is-loading="isLoading"
    @confirm="handleRestoreConfirm" @cancel="hideRestoreConfirmation" />

  <ConfirmationModal :is-open="showDeleteModal" type="delete" :item-count="selectedCount" :is-loading="isLoading"
    @confirm="handleDeleteConfirm" @cancel="hideDeleteConfirmation" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RotateCcw, Trash2 } from 'lucide-vue-next'
import ConfirmationModal from './ConfirmationModal.vue'

interface ActionBarProps {
  selectedCount: number
  isLoading?: boolean
}

withDefaults(defineProps<ActionBarProps>(), {
  isLoading: false
})

const emit = defineEmits<{
  restore: []
  forceDelete: []
}>()

// Modal states
const showRestoreModal = ref(false)
const showDeleteModal = ref(false)

// Modal handlers
const showRestoreConfirmation = () => {
  showRestoreModal.value = true
}

const hideRestoreConfirmation = () => {
  showRestoreModal.value = false
}

const showDeleteConfirmation = () => {
  showDeleteModal.value = true
}

const hideDeleteConfirmation = () => {
  showDeleteModal.value = false
}

const handleRestoreConfirm = () => {
  emit('restore')
  showRestoreModal.value = false
}

const handleDeleteConfirm = () => {
  emit('forceDelete')
  showDeleteModal.value = false
}
</script>