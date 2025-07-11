<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'

interface EmployeeCommandListPlaceholderProps {
  count?: number
}

withDefaults(defineProps<EmployeeCommandListPlaceholderProps>(), {
  count: 3,
})
</script>

<template>
  <div
    v-for="index in count"
    :key="index"
    class="flex items-center space-x-4 py-1.5"
  >
    <Skeleton class="ml-2 h-4 w-4 justify-center rounded-sm" />
    <Skeleton class="h-7 w-7 rounded-full" />
    <Skeleton class="h-6 w-[63%]" />
  </div>
</template>
