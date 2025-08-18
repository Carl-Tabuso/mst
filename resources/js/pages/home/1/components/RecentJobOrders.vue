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
import { Link } from '@inertiajs/vue3'
import { Clock } from 'lucide-vue-next'
import { RecentJobOrders } from '..'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'

interface RecentJobOrdersProps {
  data?: RecentJobOrders[]
}

defineProps<RecentJobOrdersProps>()

const { statusMap, routeMap } = useJobOrderDicts()
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
    <CardContent class="my-2 h-[375px] divide-y divide-border overflow-y-auto">
      <div
        v-for="jobOrder in data"
        :key="jobOrder.ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex flex-row items-center justify-between">
          <Link
            :href="
              route(
                `job_order.${routeMap[jobOrder.serviceType]}.edit`,
                jobOrder.ticket,
              )
            "
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ jobOrder.ticket }}
          </Link>
          <Badge
            :variant="statusMap[jobOrder.status]?.badge"
            class="border-0 p-0"
          >
            {{ statusMap[jobOrder.status].label }}
          </Badge>
        </div>
        <div
          class="flex flex-row justify-between gap-10 text-xs text-muted-foreground"
        >
          <span class="flex items-center gap-2 whitespace-nowrap">
            <Clock class="size-3" />
            {{ jobOrder.humanDiff }}
          </span>
          <span class="font-medium truncate">{{ jobOrder.frontliner }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
