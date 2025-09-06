<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import { Separator } from '@/components/ui/separator'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, JobOrderCorrection } from '@/types'
import { provide } from 'vue'
import CorrectionPageHeader from '../components/PageHeader.vue'
import FirstSection from './components/FirstSection.vue'
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

provide<number, string>('correctionId', props.data.id)
</script>

<template>
  <Head
    :title="String(data.jobOrder.ticket).concat(' - Job Order Correction ')"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <CorrectionPageHeader :correction="data" />
      <FirstSection
        :changes="data.properties"
        :job-order="data.jobOrder"
      />
      <Separator class="mb-3 w-full" />
      <SecondSection
        :changes="data.properties"
        :job-order="data.jobOrder"
      />
    </MainContainer>
  </AppLayout>
</template>
