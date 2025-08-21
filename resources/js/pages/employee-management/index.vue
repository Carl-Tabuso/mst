<script setup lang="ts">
import EmployeeCreateModal from '@/components/employee-management/EmployeeCreateModal.vue'
import EmployeeDataTable from '@/components/employee-management/EmployeeDataTable.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { EmployeeResponse } from '@/types/employee'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { columns } from './columns'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
  {
    title: 'Employee Management',
    href: '/employees',
  },
]

const employees = ref<EmployeeResponse>({
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
})

const isLoading = ref(true)

const fetchEmployees = async (
  params: {
    page?: number
    per_page?: number
    search?: string
    status?: string
    sort?: string
  } = {},
) => {
  isLoading.value = true
  try {
    const response = await axios.get<EmployeeResponse>('/employees', {
      params: {
        page: params.page || employees.value.current_page,
        per_page: params.per_page || employees.value.per_page,
        search: params.search,
        status: params.status,
        sort: params.sort,
      },
    })
    employees.value = response.data
  } catch (error) {
    console.error('Error fetching employees:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchEmployees())
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="container space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-blue-800">Employee Management</h1>
        <div class="mb-4 flex justify-end">
          <EmployeeCreateModal />
        </div>
      </div>
      <EmployeeDataTable
        :columns="columns"
        :data="employees.data"
        :pagination="{
          current_page: employees.current_page,
          last_page: employees.last_page,
          per_page: employees.per_page,
          total: employees.total,
        }"
        :isLoading="isLoading"
        @page-change="(page: any) => fetchEmployees({ page })"
        @per-page-change="(per_page: any) => fetchEmployees({ per_page, page: 1 })"
        @filter-change="(filters: { page?: number; per_page?: number; search?: string; status?: string; sort?: string } | undefined) => fetchEmployees({ ...filters, page: 1 })"
      />
    </div>
  </AppLayout>
</template>
