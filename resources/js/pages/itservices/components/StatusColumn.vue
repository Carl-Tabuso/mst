<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { Badge } from '@/components/ui/badge'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { JobOrder } from '@/types'
import { ArrowRight } from 'lucide-vue-next'

interface StatusColumnProps {
  jobOrder: JobOrder
}

const props = defineProps<StatusColumnProps>()

const status = useJobOrderDicts().statusMap[props.jobOrder.status]

const finishedStatuses = <Array<JobOrderStatus>>[
  'failed',
  'closed',
  'dropped',
  'completed',
  'successful',
]
const isFinished: boolean = finishedStatuses.includes(status.id)

const nextStepUrlMap: Partial<Record<JobOrderStatus, any>> = {
  'for check up': {
    url: route('job_order.it_service.onsite.initial.create', {
      iTService: props.jobOrder.serviceableId,
    }),
    label: 'Proceed to First Onsite',
  },
  'for final service': {
    url: route('job_order.it_service.onsite.final.create', {
      iTService: props.jobOrder.serviceableId,
    }),
    label: 'Proceed to Final Onsite',
  },
}

const nextStepUrl = nextStepUrlMap[status.id]
</script>

<template>
  <Badge
    v-if="isFinished"
    :variant="status.badge"
  >
    {{ status.label }}
  </Badge>
  <TextLink
    v-else
    :href="nextStepUrl.url"
    class="group flex flex-row items-center gap-1"
  >
    <span class="text-[13px] font-medium">
      {{ nextStepUrl.label }}
    </span>
    <ArrowRight
      class="size-4 transform transition-transform duration-200 group-hover:translate-x-1"
    />
  </TextLink>
</template>
