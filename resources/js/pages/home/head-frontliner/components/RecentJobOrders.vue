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
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { Link } from '@inertiajs/vue3'
import { ClipboardMinus, Clock } from 'lucide-vue-next'
import { RecentJobOrders } from '..'

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
        v-if="data?.length"
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
          <span class="truncate font-medium">{{ jobOrder.frontliner }}</span>
        </div>
      </div>
      <div
        v-else
        class="flex h-full flex-col items-center justify-center gap-6 p-6 font-medium text-muted-foreground"
      >
        <div class="rounded-full bg-muted p-5 text-muted-foreground">
          <ClipboardMinus class="size-20 stroke-1" />
        </div>
        <span class="flex flex-col items-center gap-1">
          <h4 class="text-xl font-bold">Quiet Week!</h4>
          <p class="text-center text-[13px] leading-5">
            No new job orders were created recently. Enjoy the breather!
          </p>
        </span>
      </div>
    </CardContent>
  </Card>
</template>
