<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'

defineProps<{
  title?: string
  description?: string
}>()
</script>

<template>
  <div class="grid h-screen grid-cols-1 lg:grid-cols-2">
    <div
      class="lg:pt-42 hidden flex-col items-start justify-center space-y-4 px-4 pt-16 text-start sm:px-8 md:space-y-6 md:px-10 md:pt-32 lg:flex"
    >
      <div class="flex aspect-square rounded-full bg-white text-sidebar-primary-foreground">
        <AppLogoIcon class="h-16 w-16 fill-current text-white dark:text-black md:h-20 md:w-20 lg:h-24 lg:w-24" />
      </div>
      <h2
        class="mb-2 border-b-2 border-tertiary pb-4 text-2xl font-bold text-primary sm:text-3xl md:mb-4 md:border-b-4 md:pb-6 md:text-4xl lg:max-w-xl lg:text-5xl"
      >
        Track, Assign, Complete.
      </h2>
      <p
        class="max-w-xs text-xs leading-relaxed text-muted-foreground sm:max-w-xl sm:text-sm md:text-base"
      >
        Your centralized system for managing job requests and field work. <br />
        Stay on top of every task with real-time updates, progress tracking, and
        team coordination.
      </p>
    </div>

    <div class="flex items-center justify-center p-2 sm:p-4 md:p-6">
      <div
        class="flex w-full flex-col justify-center space-y-6 rounded-md max-w-xs md:max-w-md md:p-12 lg:max-w-lg"
      >
        <div class="flex flex-col space-y-2 text-center pb-3">
          <h1
            v-if="title"
            class="text-xl font-medium text-foreground tracking-tight"
          >
            {{ title }}
          </h1>
          <p
            v-if="description"
            class="text-sm text-muted-foreground"
          >
            {{ description }}
          </p>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>
