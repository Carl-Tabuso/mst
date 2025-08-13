<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { CalendarClock } from 'lucide-vue-next'

const tickets = [
  {
    id: 'JO-250005080',
    frontliner: 'John Doe II',
    date: 'July 12',
  },
  {
    id: 'JO-250005081',
    frontliner: 'Jane Smith',
    date: 'July 13',
  },
  {
    id: 'JO-250005082',
    frontliner: 'Carlos Dela Cruz',
    date: 'July 14',
  },
  {
    id: 'JO-250005083',
    frontliner: 'Maria Santos',
    date: 'July 15',
  },
  {
    id: 'JO-250005084',
    frontliner: 'Robert Johnson',
    date: 'July 16',
  },
  {
    id: 'JO-250005085',
    frontliner: 'Angela Reyes',
    date: 'July 17',
  },
  {
    id: 'JO-250005086',
    frontliner: 'David Lee',
    date: 'July 18',
  },
  {
    id: 'JO-250005087',
    frontliner: 'Sofia Mendoza',
    date: 'July 19',
  },
  {
    id: 'JO-250005088',
    frontliner: 'Michael Tan',
    date: 'July 20',
  },
  {
    id: 'JO-250005089',
    frontliner: 'Isabella Cruz',
    date: 'July 21',
  },
]
</script>

<template>
  <Card class="w-full max-w-sm shadow">
    <CardHeader class="py-4">
      <div class="flex items-center gap-3">
        <CalendarClock
          class="size-9 stroke-[1.25px] text-primary will-change-transform"
        />
        <div>
          <CardTitle class="text-lg font-semibold text-primary">
            Aging Job Order Tickets
          </CardTitle>
          <CardDescription class="text-sm leading-3">
            Tickets with no updates for the past weeks.
          </CardDescription>
        </div>
      </div>
    </CardHeader>
    <Separator />
    <CardContent
      class="my-2 max-h-[375px] divide-y divide-border overflow-y-auto"
    >
      <div
        v-for="ticket in tickets"
        :key="ticket.id"
        class="py-3 transition-colors"
      >
        <div class="text-xs font-semibold text-primary underline">
          {{ ticket.id }}
        </div>
        <div
          class="flex justify-between text-xs font-medium text-muted-foreground"
        >
          <span>{{ ticket.frontliner }}</span>
          <span>Last updated in {{ ticket.date }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
