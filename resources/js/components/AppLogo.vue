<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { cn } from '@/lib/utils'
import { HTMLAttributes } from 'vue'

interface AppLogoProps {
  class?: HTMLAttributes['class']
}

const props = defineProps<AppLogoProps>()
</script>

<template>
  <div
    :class="
      cn(
        [
          'flex aspect-square size-8 items-center justify-center rounded-full bg-sidebar-primary text-sidebar-primary-foreground',
        ],
        props.class,
      )
    "
  >
    <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
  </div>
  <div class="grid flex-1 text-left text-sm leading-4">
    <span class="truncate font-semibold">JOMS</span>
    <span class="truncate text-xs tracking-tight">Hauling Services</span>
  </div>
</template>
