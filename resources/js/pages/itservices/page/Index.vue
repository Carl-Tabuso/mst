<script setup lang="ts">
import JobOrderDataTable from '@/components/main-job-orders/DataTable.vue'
import Button from '@/components/ui/button/Button.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import { Link } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import { columns } from '../types/columns'
import JobOrderServiceTypeTabs from '@/pages/job-orders/components/JobOrderServiceTypeTabs.vue'
import PageHeader from '@/pages/job-orders/components/PageHeader.vue'

const props = defineProps<{
  jobOrders: { data: JobOrder[]; meta: EloquentCollection }
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
        <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
              <PageHeader
                title="IT Service List"
                sub-title="You can manage the list of recent IT service job orders here!"
              />
              <JobOrderServiceTypeTabs />
              <JobOrderDataTable
                  :columns="columns"
                  :data="data"
                  :meta="meta"
                  route-name="job_order.it_service.index"
              />
            </div>
        </div>
    </AppLayout>
</template>
