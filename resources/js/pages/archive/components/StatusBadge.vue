<template>
    <div class="flex items-center gap-1">
        <div class="h-2 w-2 rounded-full" :class="dotColor"></div>
        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ statusText }}
        </span>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface StatusBadgeProps {
    status: 'available' | 'completed' | 'pending' | 'cancelled' | 'in-progress'
}

const props = defineProps<StatusBadgeProps>()

const statusConfig = {
    'available': {
        text: 'Status Available',
        dotColor: 'bg-green-500'
    },
    'completed': {
        text: 'Ready Complete',
        dotColor: 'bg-green-500'
    },
    'pending': {
        text: 'Pending',
        dotColor: 'bg-yellow-500'
    },
    'cancelled': {
        text: 'Cancelled',
        dotColor: 'bg-red-500'
    },
    'in-progress': {
        text: 'In Progress',
        dotColor: 'bg-blue-500'
    }
}

const statusText = computed(() => statusConfig[props.status]?.text || 'Unknown')
const dotColor = computed(() => statusConfig[props.status]?.dotColor || 'bg-gray-500')
</script>