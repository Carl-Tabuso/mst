<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
  <div
    v-for="index in 10"
    :key="index"
    class="flex items-center space-x-4 py-1.5"
  >
    <Skeleton class="h-11 w-full rounded-sm" />
  </div>
</template>
