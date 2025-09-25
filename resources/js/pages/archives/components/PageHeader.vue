<script setup lang="ts">
interface PageHeaderProps {
  archiveType: string
  subTitle: string
}

defineProps<PageHeaderProps>()
</script>

<template>
  <div class="flex flex-col">
    <h3
      class="flex flex-row items-center gap-3 text-3xl font-bold text-muted-foreground"
    >
      Archive Records
      <span>•</span>
      <p class="text-foreground">
        {{ archiveType }}
      </p>
    </h3>
    <p class="text-muted-foreground">
      {{ subTitle }}
    </p>
  </div>
</template>
