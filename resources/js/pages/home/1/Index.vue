<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem, SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AgingJobOrderTickets from './components/AgingJobOrderTickets.vue'
import EmployeeMetrics from './components/EmployeeMetrics.vue'
import LatestJobOrders from './components/LatestJobOrders.vue'
import RecentActivities from './components/RecentActivities.vue'

interface Home1Props {
  dayPart: string
  illustration: string
}

const props = defineProps<Home1Props>()

const page = usePage<SharedData>()
const employeeFirsName = page.props.auth.user.employee.first_name

const greetings = [
  {
    id: 'morning',
    label: `Good Morning, ${employeeFirsName}!`,
    color: 'text-lime-700',
  },
  {
    id: 'afternoon',
    label: `Good afternoon, ${employeeFirsName}!`,
    color: 'text-lime-700',
  },
  {
    id: 'evening',
    label: `Good evening, ${employeeFirsName}!`,
    color: 'text-white',
  },
]

const greeting = computed(() => greetings.find((g) => g.id === props.dayPart))

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
]
</script>

<template>
  <Head title="Home" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="grid items-start gap-4 sm:grid-cols-1 lg:grid-cols-3">
        <div class="col-span-2 flex flex-col gap-4">
          <div class="relative h-[173px] rounded-lg shadow">
            <img
              :src="illustration"
              loading="lazy"
              alt="Greeting Illustration"
              class="absolute inset-0 h-full w-full rounded-lg object-cover will-change-transform"
            />
            <div
              class="absolute inset-0 flex flex-col justify-start px-6 pt-12"
            >
              <div :class="['text-3xl font-bold', greeting?.color]">
                {{ greeting?.label }}
              </div>
              <div class="text-base text-white">
                Here's what's happening today!
              </div>
            </div>
          </div>
          <EmployeeMetrics />
          <LatestJobOrders />
        </div>
        <AgingJobOrderTickets />
      </div>
      <div class="mt-4 grid items-start gap-4 sm:grid-cols-1 lg:grid-cols-3">
        <div class="col-span-2"></div>
        <div>
          <RecentActivities />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
