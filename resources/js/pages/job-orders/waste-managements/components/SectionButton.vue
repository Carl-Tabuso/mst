<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { LoaderCircle } from 'lucide-vue-next'

interface SectionButtonProps {
  isSubmitBtnDisabled: boolean
}

interface SectionButtonEmits {
  onCancelSubmit: void
}

defineProps<SectionButtonProps>()

defineEmits<SectionButtonEmits>()
</script>

<template>
  <div class="flex items-center space-x-2">
    <Button
      v-show="isSubmitBtnDisabled"
      variant="outline"
      type="button"
      @click="$emit('onCancelSubmit')"
    >
      Cancel
    </Button>
    <Button
      variant="default"
      type="submit"
      :disabled="isSubmitBtnDisabled"
    >
      <LoaderCircle
        v-if="isSubmitBtnDisabled"
        class="animate-spin"
      />
      Save Changes
    </Button>    
  </div>
</template>
