<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Check } from 'lucide-vue-next'
import { computed } from 'vue'

interface StatusUpdaterProps {
  status: JobOrderStatus
}

interface StatusUpdateEmits {
  markAsUpdate: [next: JobOrderStatus]
}

const props = defineProps<StatusUpdaterProps>()

defineEmits<StatusUpdateEmits>()

interface manualStatusUpdaterType {
  id: JobOrderStatus
  next: JobOrderStatus
  label: string
}

const manualStatusUpdater: manualStatusUpdaterType[] = [
  {
    id: 'for viewing',
    next: 'for proposal',
    label: 'Mark as viewed',
  },
  {
    id: 'for proposal',
    next: 'successful',
    label: 'Mark as successful',
  },
]

const jobOrderStatus = computed(() =>
  manualStatusUpdater.find((msu) => msu.id === props.status),
)
</script>

<template>
  <Button
    type="button"
    @click="$emit('markAsUpdate', jobOrderStatus?.next)"
  >
    <Check class="mr-2" />
    {{ jobOrderStatus?.label }}
  </Button>
</template>
