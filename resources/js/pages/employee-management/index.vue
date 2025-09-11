<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, Employee } from '@/types'
import EmployeeDataTable from './components/EmployeeDataTable.vue'
import { columns } from './components/columns'
import PageHeader from './components/PageHeader.vue'

interface IndexProps {
  data: {
    data: Employee[]
    meta: EloquentCollection
  }
  emptySearchImg: string
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Employees',
    href: '/employees',
  },
]
</script>

<template>
  <Head title="Employees" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <PageHeader
          title="Employees"
          sub-title="You can manage the list of employees here!"
        />
        <EmployeeDataTable
          :columns="columns"
          :data="data.data"
          :meta="data.meta"
          :empty-img-uri="emptySearchImg"
          :routeName="'employee-management.index'"
        />
      </div>
    </div>
  </AppLayout>
</template>