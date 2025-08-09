<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import JobOrderServiceTypeTabs from '../components/JobOrderServiceTypeTabs.vue'
import PageHeader from '../components/PageHeader.vue'
import WasteManagementTable from './components/DataTable.vue'
import { columns } from './components/columns'

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
    title: 'Waste Management',
    href: '/job-orders/waste-managements',
  },
]
</script>

<template>
  <Head title="Waste Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="Waste Management"
          sub-title="You can manage the list of active waste management job orders here!"
        />
        <JobOrderServiceTypeTabs />
        <WasteManagementTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
          :empty-img-uri="emptySearchImg"
        />
      </div>
    </div>
  </AppLayout>
</template>
