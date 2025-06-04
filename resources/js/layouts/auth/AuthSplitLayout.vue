<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
  title?: string;
  description?: string;
}>();

</script>

<template>
  <div class="grid h-screen grid-cols-1 lg:grid-cols-2 bg-blue-50">
    <!-- Left column with logo and tagline -->
    <div class="hidden lg:flex flex-col justify-center items-start px-4 sm:px-8 md:px-10 space-y-4 md:space-y-6 text-start pt-16 md:pt-32 lg:pt-42">
      <AppLogoIcon class="h-16 w-16 md:h-20 md:w-20 lg:h-24 lg:w-24" />
      <h2 class="text-2xl sm:text-3xl md:text-4xl lg:max-w-xl lg:text-5xl font-bold text-blue-900 border-b-2 md:border-b-4 border-lime-500 mb-2 md:mb-4 pb-4 md:pb-6">
        Track, Assign, Complete.
      </h2>
      <p class="max-w-xs sm:max-w-xl text-neutral-600 text-xs sm:text-sm md:text-base leading-relaxed">
        Your centralized system for managing job requests and field work. <br>
        Stay on top of every task with real-time updates, progress tracking, and team coordination.
      </p>
    </div>

    <!-- Right column with login form -->
    <div class="flex items-center justify-center p-2 sm:p-4 md:p-6">
      <div
        class="w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg min-h-[400px] md:min-h-[500px] lg:min-h-[550px] bg-white p-4 sm:p-8 md:p-12 rounded-sm shadow-md flex flex-col justify-center"
      >  
        <div class="text-center pt-6 md:pt-10 lg:pt-12">
          <h1 v-if="title" class="text-sm sm:text-xs md:text-lg font-semibold text-blue-900 mb-1">{{ title }}</h1>
          <p v-if="description" class="text-[10px] sm:text-[5px] md:text-xs text-neutral-400">{{ description }}</p>
        </div>
        <slot />
      </div>
    </div>
  </div>
</template>
