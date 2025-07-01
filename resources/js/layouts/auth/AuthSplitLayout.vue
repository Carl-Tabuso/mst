<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'

defineProps<{
  title?: string
  description?: string
}>()
</script>

<template>
  <div class="grid h-screen grid-cols-1 bg-blue-50 lg:grid-cols-2">
    <!-- Left column with logo and tagline -->
    <div
      class="lg:pt-42 hidden flex-col items-start justify-center space-y-4 px-4 pt-16 text-start sm:px-8 md:space-y-6 md:px-10 md:pt-32 lg:flex"
    >
      <AppLogoIcon class="h-16 w-16 md:h-20 md:w-20 lg:h-24 lg:w-24" />
      <h2
        class="mb-2 border-b-2 border-lime-500 pb-4 text-2xl font-bold text-blue-900 sm:text-3xl md:mb-4 md:border-b-4 md:pb-6 md:text-4xl lg:max-w-xl lg:text-5xl"
      >
        Track, Assign, Complete.
      </h2>
      <p
        class="max-w-xs text-xs leading-relaxed text-neutral-600 sm:max-w-xl sm:text-sm md:text-base"
      >
        Your centralized system for managing job requests and field work. <br />
        Stay on top of every task with real-time updates, progress tracking, and
        team coordination.
      </p>
    </div>

    <!-- Right column with login form -->
    <div class="flex items-center justify-center p-2 sm:p-4 md:p-6">
      <div
        class="flex min-h-[400px] w-full max-w-xs flex-col justify-center rounded-sm bg-white p-4 shadow-md sm:max-w-sm sm:p-8 md:min-h-[500px] md:max-w-md md:p-12 lg:min-h-[550px] lg:max-w-lg"
      >
        <div class="pt-6 text-center md:pt-10 lg:pt-12">
          <h1
            v-if="title"
            class="mb-1 text-sm font-semibold text-blue-900 sm:text-xs md:text-lg"
          >
            {{ title }}
          </h1>
          <p
            v-if="description"
            class="text-[10px] text-neutral-400 sm:text-[5px] md:text-xs"
          >
            {{ description }}
          </p>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>
