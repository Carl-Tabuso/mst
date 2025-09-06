<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, JobOrderCorrection } from '@/types'
import CorrectionPageHeader from '../components/PageHeader.vue'
import JobOrderDetails from '../components/JobOrderDetails.vue'

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
</script>

<template>
  <Head
    :title="String(data.jobOrder.ticket).concat(' - Job Order Correction')"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <CorrectionPageHeader :correction="data" />
      <JobOrderDetails
        :changes="data.properties"
        :job-order="data.jobOrder"
      />
    </MainContainer>
  </AppLayout>
</template>
