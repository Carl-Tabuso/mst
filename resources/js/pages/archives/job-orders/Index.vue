<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
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
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col">
        <h3 class="scroll-m-20 text-3xl font-bold text-primary">
            Archived • Job Orders
        </h3>
        <p class="text-muted-foreground">
            Archived job orders here.
        </p>
        </div>
        <ArchivedJobOrderDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
