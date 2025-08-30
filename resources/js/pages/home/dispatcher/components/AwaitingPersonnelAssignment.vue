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
import { CalendarClock } from 'lucide-vue-next'
import { AwaitingPersonnelAssignment } from '..'

interface AwaitingPersonnelAssignmentProps {
  data?: AwaitingPersonnelAssignment[]
}

defineProps<AwaitingPersonnelAssignmentProps>()

const { routeMap } = useJobOrderDicts()
</script>

<template>
  <Card>
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-foreground">
          Awaiting Personnel Assignment
        </CardTitle>
        <CardDescription class="leading-3">
          Job orders awaiting personnel assignments
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 h-[375px] divide-y divide-border overflow-y-auto">
      <div
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
            {{ total }} pending days
          </Badge>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
