<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import { Separator } from '@/components/ui/separator'
import { useCorrections } from '@/composables/useCorrections'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, JobOrderCorrection } from '@/types'
import CorrectionPageHeader from '../components/PageHeader.vue'
import FirstSection from './components/FirstSection.vue'
import SecondSection from './components/SecondSection.vue'

interface ShowProps {
  data: JobOrderCorrection
}

const props = defineProps<ShowProps>()

const changes = props.data.properties.after

const { canCorrectProposalInformation } = useCorrections()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Order Corrections',
    href: route('job_order.correction.index'),
  },
  {
    title: 'List',
    href: route('job_order.correction.index'),
  },
  {
    title: props.data.jobOrder.ticket,
    href: '#',
  },
]
</script>

<template>
  <Head
    :title="String(data.jobOrder.ticket).concat(' - Job Order Correction ')"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <CorrectionPageHeader :correction="data" />
      <FirstSection
        :changes="changes"
        :job-order="data.jobOrder"
      />
      <div v-if="canCorrectProposalInformation(data.jobOrder.status)">
        <Separator class="mb-3 w-full" />
        <SecondSection
          :changes="changes"
          :job-order="data.jobOrder"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
