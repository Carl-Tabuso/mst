<script setup lang="ts">
defineProps<{
  employees: Array<{ id: number; name: string }>
}>()
</script>

<template>
  <div>
    {{ employees }}
  </div>
</template>