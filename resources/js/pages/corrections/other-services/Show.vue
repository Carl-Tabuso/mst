<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { correctionStatuses } from '@/constants/correction-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, JobOrderCorrection } from '@/types'
import { computed, onMounted, provide } from 'vue'
import ChangesModal from './components/ChangesModal.vue'
import FirstSection from './components/FirstSection.vue'
import ReasonCard from './components/ReasonCard.vue'
import SecondSection from './components/SecondSection.vue'

interface ShowProps {
  data: JobOrderCorrection
}

const props = defineProps<ShowProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Order Corrections',
    href: '/job-orders/corrections',
  },
  {
    title: props.data.jobOrder.ticket,
    href: '#',
  },
]

const correctionStatus = computed(() => {
  return correctionStatuses.find((status) => status.id === props.data.status)
})

const form5Changes = computed(() => {
  const beforeServiceable = props.data.properties.before?.serviceable || {}
  const afterServiceable = props.data.properties.after?.serviceable || {}

  return {
    before: beforeServiceable,
    after: afterServiceable,
  }
})

provide<number, string>('correctionId', props.data.id)

onMounted(() => {
  const properties = props.data.properties
  for (const key in properties) {
    if (properties.hasOwnProperty(key)) {
      if (typeof properties[key] === 'object' && properties[key] !== null) {
      }
    }
  }
})
</script>

<template>
  <Head
    :title="String(data.jobOrder.ticket).concat(' - Job Order Correction ')"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div>
        <div class="flex flex-row items-center justify-between gap-4 pb-2">
          <div class="flex flex-col gap-1">
            <div class="flex items-center gap-4">
              <h3 class="scroll-m-20 text-3xl font-bold text-muted-foreground">
                Correction Request for
                <span class="tracking-tighter text-foreground">
                  {{ data.jobOrder.ticket }}
                </span>
              </h3>
              <Badge
                :variant="correctionStatus?.badge"
                class="overflow-hidden truncate text-ellipsis"
              >
                {{ correctionStatus?.label }}
              </Badge>
            </div>
          </div>
          <ChangesModal
            :changes="data.properties"
            :status="data.status"
          />
        </div>
        <ReasonCard :correction="data" />
      </div>
      <FirstSection
        :changes="data.properties"
        :job-order="data.jobOrder"
      />
      <Separator class="mb-3 w-full" />
      <SecondSection
        :changes="form5Changes"
        :job-order="data.jobOrder"
      />
    </div>
  </AppLayout>
</template>
