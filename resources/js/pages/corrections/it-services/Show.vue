<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import { Separator } from '@/components/ui/separator'
import { useCorrections } from '@/composables/useCorrections'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, JobOrderCorrection } from '@/types'
import JobOrderDetails from '../components/JobOrderDetails.vue'
import CorrectionPageHeader from '../components/PageHeader.vue'
import FinalOnsiteDetails from './components/FinalOnsiteDetails.vue'
import InitialOnsiteDetails from './components/InitialOnsiteDetails.vue'
import MachineDetails from './components/MachineDetails.vue'

interface ShowProps {
  data: JobOrderCorrection
}

const props = defineProps<ShowProps>()

const changes = props.data.properties.after

const { canCorrectInitialOnsiteReport, canCorrectFinalOnsiteReport } =
  useCorrections()

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
    :title="String(data.jobOrder.ticket).concat(' - Job Order Correction')"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <CorrectionPageHeader :correction="data" />
      <JobOrderDetails
        :changes="changes"
        :job-order="data.jobOrder"
        class="mt-4"
      />
      <div>
        <Separator class="my-3 w-full" />
        <MachineDetails
          :changes="changes"
          :job-order="data.jobOrder"
        />
      </div>
      <div v-if="canCorrectInitialOnsiteReport(data.jobOrder.status)">
        <Separator class="mb-3 mt-6 w-full" />
        <InitialOnsiteDetails
          :changes="changes"
          :correction="data"
        />
      </div>
      <div v-if="canCorrectFinalOnsiteReport(data.jobOrder.status)">
        <Separator class="mb-3 mt-6 w-full" />
        <FinalOnsiteDetails
          :changes="changes"
          :job-order="data.jobOrder"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
