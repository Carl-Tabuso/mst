<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { getInitials } from '@/composables/useInitials'
import AppLayout from '@/layouts/AppLayout.vue'
import { ActivityLog, BreadcrumbItem } from '@/types'
import { router } from '@inertiajs/vue3'
import { format } from 'date-fns'
import {
  Download,
  LoaderCircle,
  MonitorSmartphone,
  RefreshCw,
} from 'lucide-vue-next'
import { ref } from 'vue'

interface IndexProps {
  activityLogs: {
    data: ActivityLog[]
    current_page: number
    last_page: number
  }
}

const props = defineProps<IndexProps>()

const changeLogs = ref<ActivityLog[]>(props.activityLogs.data)
const isFetchingLogs = ref<boolean>(false)

const onLoadMoreLogs = () => {
  router.visit(
    route('activity.index', {
      page: props.activityLogs.current_page + 1,
    }),
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      showProgress: false,
      onStart: () => (isFetchingLogs.value = true),
      onSuccess: (page: any) => {
        history.replaceState({}, '', route('activity.index'))
        pushNewLogs(page.props.activityLogs.data)
        isFetchingLogs.value = false
      },
    },
  )
}

const pushNewLogs = (newPayload: ActivityLog[]) => {
  newPayload.forEach((data: ActivityLog) => {
    const index = changeLogs.value.findIndex((log) => log.date === data.date)
    if (index !== -1) {
      changeLogs.value[index].items.push(...data.items)
    } else {
      changeLogs.value.push(data)
    }
  })
}

const onExportClick = () => window.open(route('activity.export'), '__blank')

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Activity Logs',
    href: '/activities',
  },
]
</script>

<template>
  <Head title="Activity Logs" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="scroll-m-20 text-3xl font-bold leading-7 text-primary">
            Activity Logs
          </h1>
          <p class="text-muted-foreground">
            You can monitor user actions and system events in chronological
            order.
          </p>
        </div>
        <Button
          @click="onExportClick"
          variant="outline"
        >
          <Download class="mr-2" />
          Export All
        </Button>
      </div>
      <div class="max-w-full-sm pt-12">
        <div
          v-for="(group, groupIndex) in changeLogs"
          :key="groupIndex"
          class="relative"
        >
          <div class="ml-11 flex">
            <span
              class="text-md rounded-md bg-muted px-4 py-1 font-semibold shadow-sm"
            >
              {{ format(new Date(group.date), 'MMMM d, yyyy') }}
            </span>
          </div>
          <div
            v-for="item in group.items"
            :key="item.id"
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
              <div
                class="relative flex-1 space-y-4 border-l-2 pl-6 group-last:pb-4 sm:pl-8"
              >
                <div
                  class="absolute -left-px top-4 h-3 w-3 -translate-x-1/2 rounded-full border-2 border-primary bg-background"
                />

                <div class="w-full rounded-md border p-4 shadow-md">
                  <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-3">
                      <p
                        class="text-sm font-semibold text-primary sm:text-base"
                      >
                        {{ item.description }}
                      </p>
                      <div
                        class="flex items-center gap-12 text-xs text-muted-foreground"
                      >
                        <div
                          v-if="item.causer"
                          class="flex items-center gap-3"
                        >
                          <Avatar class="h-8 w-8">
                            <AvatarImage
                              v-if="item.causer.avatar"
                              :src="item.causer.avatar"
                              alt="User"
                            />
                            <AvatarFallback>
                              {{ getInitials(item.causer.employee.fullName) }}
                            </AvatarFallback>
                          </Avatar>
                          <div class="flex flex-col leading-tight">
                            <p class="text-sm font-medium">
                              {{ item.causer.employee.fullName }}
                              <Badge
                                variant="progress"
                                class="ml-2 truncate py-0 text-xs"
                              >
                                {{
                                  `${item.causer.roles[0].name.charAt(0).toUpperCase()}${item.causer.roles[0].name.slice(1)}`
                                }}
                              </Badge>
                            </p>
                            <p>
                              {{ item.causer.email }}
                            </p>
                          </div>
                        </div>
                        <div class="flex items-center gap-3">
                          <MonitorSmartphone class="size-7 stroke-1" />
                          <div class="flex flex-col">
                            <p v-if="item.causer">
                              {{ item.platform }} - {{ item.browser }}
                            </p>
                            <template v-else>System Generated</template>
                            <p>{{ item.ipAddress }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <Badge variant="outline">{{ item.log }}</Badge>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="activityLogs.last_page > activityLogs.current_page"
        class="mb-7 ml-12 flex justify-start"
      >
        <Button
          class=""
          :disabled="isFetchingLogs"
          @click="onLoadMoreLogs"
        >
          <LoaderCircle
            v-show="isFetchingLogs"
            class="animate-spin"
          />
          <RefreshCw
            v-show="!isFetchingLogs"
            class="mr-2"
          />
          {{ isFetchingLogs ? 'Fetching more logs...' : 'Load More' }}
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
