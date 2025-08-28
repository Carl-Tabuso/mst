<script setup lang="ts">
import { onMounted } from 'vue'
import { GreetingKey, MyRecentActivites } from '..'
import { router } from '@inertiajs/vue3'
import { BreadcrumbItem } from '@/types'
import AppLayout from '@/layouts/AppLayout.vue'
import GreetingIllustration from '../components/GreetingIllustration.vue'
import MyRecentActivities from '../components/RecentActivities.vue'

interface Home7Props {
    dayPart: GreetingKey
    illustration: string
    data?: { recentActivities: MyRecentActivites[] }
}

defineProps<Home7Props>()

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
          </div>
          <MyRecentActivities :data="data?.recentActivities" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
