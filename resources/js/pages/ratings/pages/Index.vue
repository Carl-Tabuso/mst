<script setup lang="ts">
import EmployeeRatingDataTable from '@/components/employee-ratings/DataTable.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import { ref, watch } from 'vue'
import { columns } from '../types/columns'

const props = defineProps<{
  jobOrders: { data: JobOrder[]; meta: EloquentCollection }
  filters?: {
    search?: string
    statuses?: string[]
    fromDateOfService?: string
    toDateOfService?: string
  }
}>()

const data = ref<JobOrder[]>(props.jobOrders.data)
const meta = ref(props.jobOrders.meta)

watch(
  () => props.jobOrders,
  (update) => {
    data.value = update.data
    meta.value = update.meta
  },
)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Performance Evaluation',
    href: '/ratings',
  },
]
</script>

<template>
  <Head title="Performance Evaluation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-white dark:bg-zinc-900">
      <div class="px-3 py-3 sm:px-6 sm:py-6">
        <div
          class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-white p-2 dark:bg-zinc-900 sm:p-4 lg:p-6"
        >
          <div class="mb-3 flex items-center">
            <div class="flex flex-col gap-y-1">
              <h3
                class="scroll-m-20 text-2xl font-bold text-sky-900 dark:text-gray-100 sm:text-3xl lg:text-4xl"
              >
                Performance Evaluation
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 sm:text-base">
                Evaluate employees' performance based on previous assigned
                hauling
              </p>
            </div>
          </div>

          <div
            class="rounded-lg border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-zinc-900"
          >
            <EmployeeRatingDataTable
              :columns="columns"
              :data="data"
              :meta="meta"
              :filters="filters"
              route-name="employee.ratings.index"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
