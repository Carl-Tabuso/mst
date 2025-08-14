<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { Clock } from 'lucide-vue-next'
import { RecentJobOrders } from '..'

interface RecentJobOrdersProps {
  data?: RecentJobOrders[]
}

defineProps<RecentJobOrdersProps>()

const statusMap = Object.fromEntries(
  JobOrderStatuses.map((jobOrderStatus) => {
    return [jobOrderStatus.id, jobOrderStatus]
  }),
)

const path = Object.fromEntries(
  jobOrderRouteNames.map((route) => {
    return [route.id, route.route]
  }),
)
</script>

<template>
  <Card class="w-full shadow">
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-primary">
          Recent Job Orders
        </CardTitle>
        <CardDescription class="text-sm leading-3">
          Created within the past week
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent
      class="my-2 max-h-[375px] divide-y divide-border overflow-y-auto"
    >
      <div
        v-for="jobOrder in data"
        :key="jobOrder.ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex flex-row items-center justify-between">
          <a
            :href="
              route(
                `job_order.${path[jobOrder.serviceType]}.edit`,
                jobOrder.ticket,
              )
            "
            target="_blank"
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ jobOrder.ticket }}
          </a>
          <Badge
            :variant="statusMap[jobOrder.status]?.badge"
            class="border-0 p-0"
          >
            {{ statusMap[jobOrder.status].label }}
          </Badge>
        </div>
        <div
          class="flex flex-row justify-between text-xs text-muted-foreground"
        >
          <span class="flex items-center gap-2">
            <Clock class="size-3" />
            {{ jobOrder.humanDiff }}
          </span>
          <span>{{ jobOrder.frontliner }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
