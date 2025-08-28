<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { router } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { UserStatisticsCard } from '.'
import { GreetingKey } from '..'
import { RecentActivitiesCard } from '../head-frontliner'
import RecentActivities from '../head-frontliner/components/RecentActivities.vue'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import UserStatistics from './components/UserStatistics.vue'
import VitalSignsCard from './components/VitalSignsCard.vue'

interface Home2Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    userStatistics: UserStatisticsCard[]
    recentActivities: RecentActivitiesCard[]
  }
}

defineProps<Home2Props>()

const isDataLoaded = ref<boolean>(false)

onMounted(() => {
  router.reload({
    only: ['data'],
    onFinish: () => (isDataLoaded.value = !isDataLoaded.value),
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
      <div
        v-if="!isDataLoaded"
        class="flex h-full justify-center"
      >
        <LoaderCircle class="animate-spin" />
      </div>
      <div v-else>
        <div class="grid items-start gap-4 md:grid-cols-1 lg:grid-cols-3">
          <div class="flex flex-col gap-4 lg:col-span-2">
            <div>
              <GreetingIllustration
                :day-part="dayPart"
                :illustration="illustration"
              />
            </div>
            <div class="flex w-full flex-row items-start gap-4">
              <VitalSignsCard />
              <UserStatistics
                :data="data?.userStatistics"
                class="flex-1"
              />
            </div>
          </div>
          <RecentActivities :data="data?.recentActivities" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
