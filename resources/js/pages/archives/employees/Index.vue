<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, EloquentCollection, Employee, Position } from '@/types'
import { provide } from 'vue'
import ArchivedTabs from '../components/ArchivedTabs.vue'
import PageHeader from '../components/PageHeader.vue'
import ArchivedEmployeeDataTable from './components/DataTable.vue'
import { columns } from './components/columns'

interface IndexProps {
  data: {
    data: Employee[]
    meta: EloquentCollection
  }
  positions: Position[]
}

const props = defineProps<IndexProps>()

provide('positions', props.positions)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Archives',
    href: '#',
  },
  {
    title: 'Employees',
    href: '/archives/employees',
  },
]
</script>

<template>
  <Head title="Archived Employees" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div class="mb-1">
          <PageHeader
            archive-type="Employees"
            sub-title="You can manage and restore the archived employees here!"
          />
        </div>
        <ArchivedTabs />
        <ArchivedEmployeeDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
        />
      </div>
    </MainContainer>
  </AppLayout>
</template>
