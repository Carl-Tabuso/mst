<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, JobOrder } from '@/types'
import { Head } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { columns } from '@/components/job-orders/columns'
import JobOrderDataTable from '@/components/job-orders/DataTable.vue'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Link } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'

const props = defineProps<{ 
  jobOrders: { data: JobOrder[], links: JobOrder[], meta: JobOrder[] } 
}>()

const data = ref<JobOrder[]>(props.jobOrders.data)

const links = ref<{}>(props.jobOrders.links)

const meta = ref<{}>(props.jobOrders.meta)

watch(() => props.jobOrders, (newVal) => {
  data.value = newVal.data
  links.value = newVal.data
  meta.value = newVal.meta
})

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '#',
  },
  {
    title: 'List',
    href: '/job_orders',
  }
]
</script>

<template>
    <Head title="Job Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex items-center">
          <div class="flex flex-col gap-y-1">
            <h3 class="scroll-m-20 text-3xl font-bold">
              Job Order List
            </h3>
            <p class="text-sm">
              Manage all existing job orders here.
            </p>          
          </div>
          <div class="ml-auto">
            <Button variant="default" class="">
              <Plus class="mr-2" />
              Create Job Order
            </Button>          
          </div>
        </div>
        <Tabs default-value="all" class="w-full">
          <TabsList class="flex justify-start">
            <Link :href="route('job_order.index')">
              <TabsTrigger value="all" class="px-7">
                All
              </TabsTrigger>
            </Link>
            <Link :href="route('job_order.waste_management')">
              <TabsTrigger value="waste management" class="px-7">
                Waste Management
              </TabsTrigger>
            </Link>
            <Link :href="route('job_order.it_service')">
              <TabsTrigger value="it services" class="px-7">
                IT Services
              </TabsTrigger>
            </Link>
            <Link :href="route('job_order.others')">
              <TabsTrigger value="others" class="px-7">
                Other Services
              </TabsTrigger>
            </Link>
          </TabsList>
        </Tabs>
        <JobOrderDataTable
          :columns="columns"
          :data="data"
          :links="links"
          :meta="meta" />
      </div>
    </AppLayout>
</template>