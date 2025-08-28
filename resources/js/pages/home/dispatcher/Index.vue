<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { CurrentYearParticipation, GreetingKey, MyRecentActivites } from '..'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'
import ParticipationOverview from '../components/ParticipationOverview.vue'

interface Home4Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    recentActivities: MyRecentActivites[]
    currentYearParticipation: CurrentYearParticipation[]
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
            <ParticipationOverview :data="data?.currentYearParticipation" />
          </div>
          <MyRecentActivities
            :data="data?.recentActivities"
            content-height="h-[490px]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
