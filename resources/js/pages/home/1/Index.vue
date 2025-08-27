<script setup lang="ts">
import Home1Placeholder from '@/components/placeholders/Home1Placeholder.vue'
import { Separator } from '@/components/ui/separator'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { router } from '@inertiajs/vue3'
import { onMounted, onUpdated, ref } from 'vue'
import {
  AgingJobOrderOrdersCard,
  AwaitingCorrectionReviewsCard,
  RecentActivitiesCard,
  RecentJobOrders as RecentJobOrdersType,
} from '.'
import { EmployeeStatistics, GreetingKey, JobOrderServiceTypeCards } from '..'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import AgingJobOrderTickets from './components/AgingJobOrderTickets.vue'
import AwaitingCorrectionReviews from './components/AwaitingCorrectionReviews.vue'
import EmployeeMetrics from './components/EmployeeMetrics.vue'
import LatestJobOrders from './components/LatestJobOrders.vue'
import RecentActivities from './components/RecentActivities.vue'
import RecentJobOrders from './components/RecentJobOrders.vue'

interface Home1Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    employeeMetrics: EmployeeStatistics[]
    latestFromJobOrderCards: JobOrderServiceTypeCards[]
    recentActivities: RecentActivitiesCard[]
    recentJobOrders: RecentJobOrdersType[]
    awaitingCorrectionReviews: AwaitingCorrectionReviewsCard[]
    agingJobOrderTickets: AgingJobOrderOrdersCard[]
  }
}

const props = defineProps<Home1Props>()

const isLoading = ref<boolean>(true)

onMounted(() => fetchPageData())

onUpdated(() => {
  if (!props.data) {
    isLoading.value = !isLoading.value
    fetchPageData()
  }
})

const fetchPageData = () => {
  router.reload({
    only: ['data'],
    onFinish: () => (isLoading.value = !isLoading.value),
  })
}

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
      <Home1Placeholder v-if="isLoading" />
      <div v-else>
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
          </div>
          <div class="">
            <RecentActivities :data="data?.recentActivities" />
          </div>
        </div>
        <Separator class="my-4" />
        <div
          class="mt-4 grid items-start gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
        >
          <div>
            <RecentJobOrders :data="data?.recentJobOrders" />
          </div>
          <div>
            <AwaitingCorrectionReviews
              :data="data?.awaitingCorrectionReviews"
            />
          </div>
          <div>
            <AgingJobOrderTickets :data="data?.agingJobOrderTickets" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
