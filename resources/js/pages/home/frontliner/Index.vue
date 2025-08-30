<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { JobOrderCorrectionRequestStatus } from '.'
import { GreetingKey, JobOrderServiceTypeCards, MyRecentActivites } from '..'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'
import MyJobOrderStatistics from './components/CreatedJobOrders.vue'
import JobOrderCorrectionRequests from './components/JobOrderCorrectionRequests.vue'

interface RegularHomeProps {
  dayPart: GreetingKey
  illustration: string
  data?: {
    recentActivities: MyRecentActivites[]
    createdJobOrderStatistics: JobOrderServiceTypeCards[]
    jobOrderCorrectionStatistics: JobOrderCorrectionRequestStatus[]
  }
}

defineProps<RegularHomeProps>()

onMounted(() => {
  router.reload({
    only: ['data'],
  })
})
</script>

<template>
  <Head title="Home" />

  <AppLayout>
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
              <MyJobOrderStatistics :data="data?.createdJobOrderStatistics" />
            </div>
            <div>
              <JobOrderCorrectionRequests
                :data="data?.jobOrderCorrectionStatistics"
              />
            </div>
          </div>
          <MyRecentActivities
            :data="data?.recentActivities"
            class="h-[387px]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
