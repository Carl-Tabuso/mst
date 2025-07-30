<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { format } from 'date-fns'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Activity Logs',
    href: '/activities',
  },
]

interface IndexProps {
  activityLogs: [
    {
      date: string
      items: {
        title: string
        time: string
        log: string
        description: string
        ipAddress: string
        browser: string
        platform: string
      }[]
    },
  ]
}

const props = defineProps<IndexProps>()
</script>

<template>
  <Head title="Activity Logs" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-6">
      <h1 class="scroll-m-20 text-3xl font-bold leading-7 text-primary">
        Activity Log
      </h1>
      <p class="text-muted-foreground">
        Monitor user actions and system events in chronological order.
      </p>
      <div class="max-w-full-sm px-6 py-12">
        <div
          v-for="(group, groupIndex) in activityLogs"
          :key="groupIndex"
          class="relative"
        >
          <div class="flex">
            <span
              class="text-md rounded-sm bg-muted px-3 py-1 font-semibold shadow-sm"
            >
              {{ format(new Date(group.date), 'MMMM d, yyyy') }}
            </span>
          </div>
          <div
            v-for="(item, index) in group.items"
            :key="index"
            class="group relative"
          >
            <div class="flex items-start">
              <div
                class="mr-5 mt-3 flex w-[75px] shrink-0 flex-col gap-2 text-end sm:w-[90px]"
              >
                <span class="text-xs text-muted-foreground sm:text-sm">
                  {{ item.time }}
                </span>
              </div>
              <div class="relative space-y-2 border-l-2 pb-10 pl-6 group-last:pb-4 sm:pl-8 flex-1">
                <div class="absolute -left-px top-4 h-3 w-3 -translate-x-1/2 rounded-full border-2 border-primary bg-background" />

                <div class="border rounded-md p-4 w-full">
                  <h3 class="text-lg font-semibold sm:text-xl">
                    {{ item.title }}
                  </h3>

                  <p class="text-sm text-muted-foreground sm:text-base">
                    {{ item.description }}
                  </p>

                  <div class="flex items-center gap-2 text-xs text-muted-foreground">
                    <p>{{ item.platform }} - {{ item.browser }}</p>
                    <span> • </span>
                    <p>{{ item.ipAddress }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
