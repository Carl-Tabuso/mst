<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import ArchivedTabs from '../components/ArchivedTabs.vue'
import PageHeader from '../components/PageHeader.vue'
import { columns } from './components/columns'
import ArchivedJobOrderDataTable from './components/DataTable.vue'

interface IndexProps {
  data: {
    data: JobOrder[]
    meta: EloquentCollection
  }
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Archives',
    href: '#',
  },
  {
    title: 'Job Orders',
    href: '/archives/job-orders',
  },
]
</script>

<template>
  <Head title="Archived Job Orders" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-1">
          <PageHeader
            archive-type="Job Orders"
            sub-title="You can manage and restore the archived job orders here!"
          />
        </div>
        <ArchivedTabs />
        <ArchivedJobOrderDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
