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
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { Clock } from 'lucide-vue-next'

const tickets = [
  {
    id: 'JO-250005080',
    frontliner: 'John Doe II',
    date: 'July 12',
    status: 'for appraisal',
  },
  {
    id: 'JO-250005081',
    frontliner: 'Jane Smith',
    date: 'July 13',
    status: 'for viewing',
  },
  {
    id: 'JO-250005082',
    frontliner: 'Carlos Dela Cruz',
    date: 'July 14',
    status: 'in-progress',
  },
  {
    id: 'JO-250005083',
    frontliner: 'Maria Santos',
    date: 'July 15',
    status: 'closed',
  },
  {
    id: 'JO-250005084',
    frontliner: 'Robert Johnson',
    date: 'July 16',
    status: 'successful',
  },
  {
    id: 'JO-250005085',
    frontliner: 'Angela Reyes',
    date: 'July 17',
    status: 'for proposal',
  },
  {
    id: 'JO-250005086',
    frontliner: 'David Lee',
    date: 'July 18',
    status: 'for appraisal',
  },
  {
    id: 'JO-250005087',
    frontliner: 'Sofia Mendoza',
    date: 'July 19',
    status: 'in-progress',
  },
  {
    id: 'JO-250005088',
    frontliner: 'Michael Tan',
    date: 'July 20',
    status: 'for viewing',
  },
  {
    id: 'JO-250005089',
    frontliner: 'Isabella Cruz',
    date: 'July 21',
    status: 'for proposal',
  },
]

const getStatus = (status: string) => {
  return JobOrderStatuses.find((joStatus) => joStatus.id === status)
}
</script>

<template>
  <Card class="w-full shadow transition-shadow hover:shadow-md">
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
    <CardContent
      class="my-2 max-h-[375px] divide-y divide-border overflow-y-auto"
    >
      <div
        v-for="ticket in tickets"
        :key="ticket.id"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex items-center justify-between">
          <span
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ ticket.id }}
          </span>
          <Badge
            :variant="getStatus(ticket.status)?.badge"
            class="border-0 p-0"
          >
            {{ getStatus(ticket.status)?.label }}
          </Badge>
        </div>
        <div class="flex justify-between text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <Clock class="size-3" /> Last updated {{ ticket.date }}
          </span>
          <span class="font-medium">{{ ticket.frontliner }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
