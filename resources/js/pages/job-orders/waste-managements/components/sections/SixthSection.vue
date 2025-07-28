<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
      <div class="text-xl font-semibold leading-6">Reason For Correction</div>
      <p class="text-sm text-muted-foreground">
        You can tell the reason or declare the requested changes here.
      </p>
    </div>
    <div class="flex flex-col gap-1">
      <Textarea
        id="reason"
        required
        placeholder="Type here..."
        v-model="reason"
        :class="['w-full', 
          { 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': error }
        ]"
      />
      <InputError :message="error" />
    </div>
  </div>
</template>
