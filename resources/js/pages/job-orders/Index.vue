<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import { columns } from './components/columns'
import JobOrderDataTable from './components/DataTable.vue'
import JobOrderServiceTypeTabs from './components/JobOrderServiceTypeTabs.vue'
import PageHeader from './components/PageHeader.vue'

interface IndexProps {
  data: {
    data: JobOrder[]
    meta: EloquentCollection
  }
  emptySearchImg: string
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders',
  },
  {
    title: 'List',
    href: '/job-orders',
  },
]
</script>

<template>
  <Head title="Job Orders" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="Job Order List"
          sub-title="You can manage the list of any service types of active job orders here!"
        />
        <JobOrderServiceTypeTabs />
        <JobOrderDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
          :emptyImgUri="emptySearchImg"
        />
      </div>
    </div>
  </AppLayout>
</template>
