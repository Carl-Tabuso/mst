<script setup lang="ts">
import { Alert } from '@/components/ui/alert'
import AlertDescription from '@/components/ui/alert/AlertDescription.vue'
import { Info } from 'lucide-vue-next'

interface FormAreaInfoProps {
  condition: boolean
}

defineProps<FormAreaInfoProps>()
</script>

<template>
  <Alert
    v-if="condition"
    variant="info"
    class="w-full"
  >
    <AlertDescription class="flex w-full items-center font-normal">
      <Info
        :size="16"
        class="mr-2"
      />
      <div class="flex-1">
        <slot />
      </div>
    </AlertDescription>
  </Alert>
</template>
