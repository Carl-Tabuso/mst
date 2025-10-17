<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, EloquentCollection, Employee, Truck } from '@/types'
import { provide } from 'vue'
import ArchivedTabs from '../components/ArchivedTabs.vue'
import PageHeader from '../components/PageHeader.vue'
import ArchivedTruckDataTable from './components/DataTable.vue'
import { columns } from './components/columns'

interface IndexProps {
  data: {
    data: Truck[]
    meta: EloquentCollection
  }
  dispatchers: Employee[]
}

const props = defineProps<IndexProps>()

provide<Employee[], string>('dispatchers', props.dispatchers)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Archives',
    href: '#',
  },
  {
    title: 'Trucks',
    href: route('archive.truck.index'),
  },
]
</script>

<template>
  <Head title="Archived Trucks" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-1">
          <PageHeader
            archive-type="Trucks"
            sub-title="You can manage and restore the archived trucks here!"
          />
        </div>
        <ArchivedTabs />
        <ArchivedTruckDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
