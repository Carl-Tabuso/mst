<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm transition-opacity"
            @click="handleCancel"></div>

        <!-- Modal -->
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-xl p-6 mx-4 max-w-md w-full animate-in zoom-in-95 duration-200">
            <!-- Icon and Header -->
            <div class="flex items-start gap-4 mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full flex-shrink-0" :class="iconBgClass">
                    <component :is="icon" class="h-6 w-6" :class="iconClass" />
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-semibold mb-2" :class="titleClass">
                        {{ title }}
                    </h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p v-if="type === 'delete'" class="font-semibold text-red-600 dark:text-red-400">
                            ⚠️ This action cannot be undone!
                        </p>
                        <p>{{ message }}</p>
                        <p v-if="itemCount > 0" class="font-medium">
                            {{ itemCount }} item{{ itemCount > 1 ? 's' : '' }} will be {{ actionText }}.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end">
                <button @click="handleCancel" :disabled="isLoading"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                    Cancel
                </button>

                <button @click="handleConfirm" :disabled="isLoading"
                    class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 flex items-center gap-2 transition-colors"
                    :class="confirmButtonClass">
                    <div v-if="isLoading"
                        class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                    <component v-else :is="icon" class="h-4 w-4" />
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { RotateCcw, Trash2 } from 'lucide-vue-next'

interface ConfirmationModalProps {
    isOpen: boolean
    type: 'restore' | 'delete'
    itemCount: number
    isLoading?: boolean
}

const props = withDefaults(defineProps<ConfirmationModalProps>(), {
    isLoading: false
})

const emit = defineEmits<{
    confirm: []
    cancel: []
}>()

// Computed properties for dynamic styling and content
const icon = computed(() => {
    return props.type === 'restore' ? RotateCcw : Trash2
})

const iconBgClass = computed(() => {
    return props.type === 'restore'
        ? 'bg-green-100 dark:bg-green-900/30'
        : 'bg-red-100 dark:bg-red-900/30'
})

const iconClass = computed(() => {
    return props.type === 'restore'
        ? 'text-green-600 dark:text-green-400'
        : 'text-red-600 dark:text-red-400'
})

const titleClass = computed(() => {
    return props.type === 'restore'
        ? 'text-gray-900 dark:text-gray-100'
        : 'text-red-600 dark:text-red-400'
})

const title = computed(() => {
    return props.type === 'restore'
        ? 'Confirm Restore'
        : 'Confirm Permanent Delete'
})

const message = computed(() => {
    if (props.type === 'restore') {
        return `Are you sure you want to restore the selected item${props.itemCount > 1 ? 's' : ''}? This action will move them back to their respective active lists.`
    } else {
        return `Are you sure you want to permanently delete the selected item${props.itemCount > 1 ? 's' : ''}? This action cannot be undone and the data will be lost forever.`
    }
})

const actionText = computed(() => {
    return props.type === 'restore' ? 'restored' : 'permanently deleted'
})

const confirmText = computed(() => {
    if (props.isLoading) {
        return props.type === 'restore' ? 'Restoring...' : 'Deleting...'
    }
    return props.type === 'restore'
        ? `Restore ${props.itemCount} Item${props.itemCount > 1 ? 's' : ''}`
        : 'Delete Forever'
})

const confirmButtonClass = computed(() => {
    return props.type === 'restore'
        ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
        : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
})

const handleConfirm = () => {
    if (!props.isLoading) {
        emit('confirm')
    }
}

const handleCancel = () => {
    if (!props.isLoading) {
        emit('cancel')
    }
}
</script>

<style scoped>
@keyframes zoom-in-95 {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-in {
    animation: zoom-in-95 0.2s ease-out;
}
</style>