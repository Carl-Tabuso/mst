<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import {
  EmployeeStatistics,
  GreetingKey,
  JobOrderServiceTypeCards,
  MyRecentActivites,
} from '..'
import EmployeeMetrics from '../components/EmployeeMetrics.vue'
import LatestJobOrders from '../components/LatestJobOrders.vue'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/RecentActivities.vue'

interface Home4Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    employeeMetrics: EmployeeStatistics[]
    latestFromJobOrderCards: JobOrderServiceTypeCards[]
    recentActivities: MyRecentActivites[]
  }
}

defineProps<Home4Props>()

onMounted(() => {
  router.reload({
    only: ['data'],
  })
})

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
      <div>
        <div class="grid items-start gap-4 md:grid-cols-1 lg:grid-cols-3">
          <div class="flex flex-col gap-4 lg:col-span-2">
            <div>
              <GreetingIllustration
                :day-part="dayPart"
                :illustration="illustration"
              />
            </div>
            <div>
              <EmployeeMetrics :data="data?.employeeMetrics" />
            </div>
            <div>
              <LatestJobOrders :data="data?.latestFromJobOrderCards" />
            </div>
            <TextLink
              :href="route('report.index')"
              class="flex items-center justify-end text-xs"
            >
              See Reports and Analytics
            </TextLink>
          </div>
          <MyRecentActivities
            :data="data?.recentActivities"
            content-height="h-[435px]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
