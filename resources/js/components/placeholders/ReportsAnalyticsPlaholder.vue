<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
  <div class="mt-6 flex flex-col gap-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
      <Skeleton
        v-for="i in 4"
        :key="i"
        class="h-36 rounded-lg"
      />
    </div>
    <div class="sm-grid-cols-1 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Skeleton class="col-span-1 h-80 rounded-lg" />
      <Skeleton class="col-span-2 h-80 rounded-lg" />
    </div>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
      <Skeleton class="h-64 rounded-lg" />
      <Skeleton class="h-64 rounded-lg" />
    </div>
  </div>
</template>
