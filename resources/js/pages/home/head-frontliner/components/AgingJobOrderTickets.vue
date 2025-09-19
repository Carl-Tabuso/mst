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
import { CalendarCheck2, Clock } from 'lucide-vue-next'
import { AgingJobOrderOrdersCard } from '..'

interface AgingJobOrderTicketsProps {
  data?: AgingJobOrderOrdersCard[]
}

defineProps<AgingJobOrderTicketsProps>()

const { statusMap, routeMap } = useJobOrderDicts()
</script>

<template>
  <Card class="w-full shadow">
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-primary">
          Aging Job Order Tickets
        </CardTitle>
        <CardDescription class="text-sm leading-3 text-muted-foreground">
          Tickets with no updates for the past weeks
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 h-[375px] divide-y divide-border overflow-y-auto">
      <div
        v-if="data?.length"
        v-for="agingJobOrder in data"
        :key="agingJobOrder.ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex items-center justify-between">
          <Link
            :href="
              route(
                `job_order.${routeMap[agingJobOrder.serviceType]}.edit`,
                agingJobOrder.ticket,
              )
            "
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ agingJobOrder.ticket }}
          </Link>
          <Badge
            :variant="statusMap[agingJobOrder.status].badge"
            class="border-0 p-0"
          >
            {{ statusMap[agingJobOrder.status].label }}
          </Badge>
        </div>
        <div class="flex justify-between gap-10 text-xs text-muted-foreground">
          <span class="flex items-center gap-2 whitespace-nowrap">
            <Clock class="size-3" /> Last updated
            {{ agingJobOrder.lastUpdated }}
          </span>
          <span class="truncate font-medium">{{
            agingJobOrder.frontliner
          }}</span>
        </div>
      </div>
      <div
        v-else
        class="flex h-full flex-col items-center justify-center gap-6 p-6 font-medium text-muted-foreground"
      >
        <div class="rounded-full bg-muted p-5 text-muted-foreground">
          <CalendarCheck2 class="size-20 stroke-1" />
        </div>
        <span class="flex flex-col items-center gap-1">
          <h4 class="text-xl font-bold">All Caught Up!</h4>
          <p class="text-center text-[13px] leading-5">
            No job orders have been idle in the past weeks.
          </p>
        </span>
      </div>
    </CardContent>
  </Card>
</template>
