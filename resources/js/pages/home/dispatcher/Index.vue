<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { AwaitingPersonnelAssignment as AwaitingPersonnelAssignmentType } from '.'
import { CurrentYearParticipation, GreetingKey, MyRecentActivites } from '..'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'
import ParticipationOverview from '../components/ParticipationOverview.vue'
import AwaitingPersonnelAssignment from './components/AwaitingPersonnelAssignment.vue'

interface Home4Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    recentActivities: MyRecentActivites[]
    currentYearParticipation: CurrentYearParticipation[]
    awaitingPersonnelAssignment: AwaitingPersonnelAssignmentType[]
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
            <ParticipationOverview :data="data?.currentYearParticipation" />
          </div>
          <MyRecentActivities
            :data="data?.recentActivities"
            content-height="h-[490px]"
          />
        </div>
      </div>
      <div class="mt-4 grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        <div>
          <AwaitingPersonnelAssignment
            :data="data?.awaitingPersonnelAssignment"
          />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
