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
import { Clock } from 'lucide-vue-next'
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
        <div class="flex justify-between text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <Clock class="size-3" /> Last updated
            {{ agingJobOrder.lastUpdated }}
          </span>
          <span class="font-medium">{{ agingJobOrder.frontliner }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
