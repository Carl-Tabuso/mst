<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea'
import { useTemplateRef } from 'vue'

interface SixthSectionProps {
  status: string
}

defineProps<SixthSectionProps>()

const remarksInput = useTemplateRef('remarksInput')

defineExpose({ remarksInput })

const remarks = defineModel<string>('remarks')
</script>

<template>
  <div class="grid gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6">Remarks</div>
      <p class="text-sm text-muted-foreground">
        You can tell the reason why the job order is {{ status }}.
      </p>
    </div>
    <div class="mx-6">
      <Textarea
        ref="remarksInput"
        id="remarks"
        required
        placeholder="Enter client's complete address"
        v-model="remarks"
        class="w-full"
      />
    </div>
  </div>
</template>
