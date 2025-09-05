<script setup lang="ts">
import JobOrderDataTable from '@/components/main-job-orders/DataTable.vue'
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import JobOrderServiceTypeTabs from '@/pages/job-orders/components/JobOrderServiceTypeTabs.vue'
import PageHeader from '@/pages/job-orders/components/PageHeader.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import { columns } from '../types/columns'

defineProps<{
  jobOrders: { data: JobOrder[]; meta: EloquentCollection }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'IT Services',
    href: '/it-services',
  },
  {
    title: 'List',
    href: '/it-services',
  },
]
</script>

<template>
  <Head title="IT Services" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="IT Service List"
          sub-title="You can manage the list of recent IT service job orders here!"
        />
        <JobOrderServiceTypeTabs />
        <JobOrderDataTable
          :columns="columns"
          :data="jobOrders.data"
          :meta="jobOrders.meta"
          route-name="job_order.it_service.index"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
