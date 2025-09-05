<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import { EmployeeStatistics, GreetingKey, MyRecentActivites } from '..'
import EmployeeMetrics from '../components/EmployeeMetrics.vue'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/MyRecentActivities.vue'

interface Home5Props {
  dayPart: GreetingKey
  illustration: string
  data?: {
    employeeMetrics: EmployeeStatistics[]
    recentActivities: MyRecentActivites[]
  }
}

defineProps<Home5Props>()

onMounted(() => router.reload({ only: ['data'] }))
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
          </div>
          <MyRecentActivities :data="data?.recentActivities" />
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
