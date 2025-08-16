<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
  <div class="mt-3 flex flex-col gap-4">
    <div class="grid items-start gap-4 md:grid-cols-1 lg:grid-cols-3">
      <div class="flex flex-col gap-4 lg:col-span-2">
        <Skeleton class="h-44 rounded-lg" />
        <Skeleton class="h-80 rounded-lg" />
      </div>
      <Skeleton class="h-full min-h-[300px] rounded-lg" />
    </div>
    <div
      class="grid items-start gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
    >
      <Skeleton class="h-[500px] rounded-lg" />
      <Skeleton class="h-[500px] rounded-lg" />
      <Skeleton class="h-[500px] rounded-lg" />
    </div>
  </div>
</template>
