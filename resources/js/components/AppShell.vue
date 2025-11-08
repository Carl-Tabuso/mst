<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar'
import { onBeforeMount, ref } from 'vue'
import { Toaster } from './ui/sonner'

interface Props {
  variant?: 'header' | 'sidebar'
}

defineProps<Props>()

const isOpen = ref(true)

onBeforeMount(() => {
  isOpen.value = localStorage.getItem('sidebar') !== 'false'
})

const handleSidebarChange = (open: boolean) => {
  isOpen.value = open
  localStorage.setItem('sidebar', String(open))
}
</script>

<template>
  <div
    v-if="variant === 'header'"
    class="flex min-h-screen w-full flex-col"
  >
    <Toaster
      position="bottom-left"
      rich-colors
    />
    <slot />
  </div>
  <SidebarProvider
    v-else
    :default-open="isOpen"
    :open="isOpen"
    @update:open="handleSidebarChange"
  >
    <Toaster
      position="bottom-left"
      rich-colors
    />
    <slot />
  </SidebarProvider>
</template>
