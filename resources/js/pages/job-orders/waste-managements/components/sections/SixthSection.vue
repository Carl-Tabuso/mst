<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea'

interface SixthSectionProps {
  status: string
  error?: string
}

defineProps<SixthSectionProps>()

const reason = defineModel<string>('reason')
</script>

<template>
  <div class="grid gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6">Reason</div>
      <p class="text-sm text-muted-foreground">
        You can tell the reason or.
      </p>
    </div>
    <div class="mx-6">
      <Textarea
        id="reason"
        placeholder="Enter client's complete address"
        v-model="reason"
        :class="['w-full', 
          { 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': error }
        ]"
      />
    </div>
  </div>
</template>
