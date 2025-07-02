<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem, EloquentCollection, JobOrder } from '@/types'
import { ref, watch } from 'vue'
import { columns } from '..//types/columns' 
import JobOrderDataTable from '@/components/employee-ratings/DataTable.vue'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Link } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'

const props = defineProps<{
    jobOrders: { data: JobOrder[], meta: EloquentCollection }
}>()

const data = ref<JobOrder[]>(props.jobOrders.data)
const meta = ref(props.jobOrders.meta)

watch(() => props.jobOrders, (update) => {
    data.value = update.data
    meta.value = update.meta
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Performance Evaluation',
        href: '/ratings',
    }
]
</script>

<template>

    <Head title="Performance Evaluation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-3 py-3">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div class="flex items-center mb-3">
                    <div class="flex flex-col gap-y-1">
                        <h3 class="scroll-m-20 text-3xl font-bold">
                            Performance Evaluation
                        </h3>
                        <p class="text-muted-foreground">
                            Evaluate employees’ performance based on previous assigned hauling
                        </p>
                    </div>
                </div>
                <!-- <Tabs :model-value="route().current()" class="w-full">
                    <TabsList class="flex justify-start">
                        <Link :href="route('job_order.index')">
                        <TabsTrigger value="job_order.index" class="px-7">
                            All
                        </TabsTrigger>
                        </Link>
                        <Link :href="route('job_order.waste_management')">
                        <TabsTrigger value="waste_management" class="px-7">
                            Waste Management
                        </TabsTrigger>
                        </Link>
                        <Link :href="route('job_order.it_service')">
                        <TabsTrigger value="job_order.it_service" class="px-7">
                            Performance Evaluation
                        </TabsTrigger>
                        </Link>
                        <Link :href="route('job_order.others')">
                        <TabsTrigger value="job_order.others" class="px-7">
                            Other Services
                        </TabsTrigger>
                        </Link>
                    </TabsList>
                </Tabs> -->
                <JobOrderDataTable :columns="columns" :data="data" :meta="meta" route-name="employee.ratings" />
            </div>
        </div>
    </AppLayout>
</template>