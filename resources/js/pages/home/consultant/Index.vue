<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import TextLink from '@/components/TextLink.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import {
  EmployeeStatistics,
  GreetingKey,
  JobOrderServiceTypeCards,
  MyRecentActivites,
} from '..'
import EmployeeMetrics from '../components/EmployeeMetrics.vue'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import LatestJobOrders from '../components/LatestJobOrders.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'

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
</script>

<template>
  <Head title="Home" />

  <AppLayout>
    <MainContainer>
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
            class="h-[435px]"
          />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
