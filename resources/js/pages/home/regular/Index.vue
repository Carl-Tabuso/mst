<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { CurrentYearParticipation, GreetingKey, MyRecentActivites } from '..'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'
import ParticipationOverview from '../components/ParticipationOverview.vue'

interface RegularHomeProps {
  dayPart: GreetingKey
  illustration: string
  data?: {
    recentActivities: MyRecentActivites[]
    currentYearParticipation: CurrentYearParticipation[]
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
            <ParticipationOverview :data="data?.currentYearParticipation" />
          </div>
          <MyRecentActivities :data="data?.recentActivities" />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
