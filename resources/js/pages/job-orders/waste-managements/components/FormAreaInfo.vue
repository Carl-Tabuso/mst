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
  >
    <AlertDescription class="flex items-center font-normal">
      <Info
        :size="16"
        class="mr-2"
      />
      <slot />
    </AlertDescription>
  </Alert>
</template>
