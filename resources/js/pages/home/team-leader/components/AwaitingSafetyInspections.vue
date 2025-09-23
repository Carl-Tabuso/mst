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
import { CalendarClock, ListChecks } from 'lucide-vue-next'
import { AwaitingSafetyInspection } from '..'

interface AwaitingSafetyInspectionsProps {
  data?: AwaitingSafetyInspection[]
}

defineProps<AwaitingSafetyInspectionsProps>()

const { routeMap } = useJobOrderDicts()
</script>

<template>
  <Card>
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-foreground">
          Awaiting Safety Inspection
        </CardTitle>
        <CardDescription class="leading-3">
          Your unfinished safety inspection checklists.
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 h-[375px] divide-y divide-border overflow-y-auto">
      <div
        v-if="data?.length"
        v-for="{ ticket, total, duration, inDays, serviceType } in data"
        :key="ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex flex-row items-center justify-between">
          <div class="space-y-1">
            <Link
              :href="route(`job_order.${routeMap[serviceType]}.edit`, ticket)"
              class="text-sm font-medium tracking-tighter text-primary hover:underline"
            >
              {{ ticket }}
            </Link>
            <span
              class="flex items-center gap-2 whitespace-nowrap text-xs text-muted-foreground"
            >
              <CalendarClock class="size-3" />
              {{ duration }}
              <span class="font-semibold"> {{ inDays }} days </span>
            </span>
          </div>
          <Badge
            variant="continuous"
            class="border-0 p-0"
          >
            {{ total }} checklists
          </Badge>
        </div>
      </div>
      <div
        v-else
        class="flex h-full flex-col items-center justify-center gap-6 p-6 font-medium text-muted-foreground"
      >
        <div class="rounded-full bg-muted p-5 text-muted-foreground">
          <ListChecks class="size-20 stroke-1" />
        </div>
        <span class="flex flex-col items-center gap-1">
          <h4 class="text-xl font-bold">Safe & Sound!</h4>
          <p class="text-center text-[13px] leading-5">
            All inspection checklists are up to date.
          </p>
        </span>
      </div>
    </CardContent>
  </Card>
</template>
