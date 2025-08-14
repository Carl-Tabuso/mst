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
import { Clock } from 'lucide-vue-next'

const data = [
  {
    ticket: 'JO-250005081',
    status: 'for appraisal',
    requestedAt: 'August 10',
    frontliner: 'Carlos Dela Cruz',
    fieldCount: 5,
  },
  {
    ticket: 'JO-250005082',
    status: 'for viewing',
    requestedAt: 'August 11',
    frontliner: 'Maria Santos',
    fieldCount: 6,
  },
  {
    ticket: 'JO-250005088',
    status: 'for appraisal',
    requestedAt: 'July 12',
    frontliner: 'Carlos Dela Cruz',
    fieldCount: 2,
  },
  {
    ticket: 'JO-250005090',
    status: 'in-progress',
    requestedAt: 'July 12',
    frontliner: 'Maria Santos',
    fieldCount: 5,
  },
  {
    ticket: 'JO-250005083',
    status: 'closed',
    requestedAt: 'July 12',
    frontliner: 'Robert Johnson',
    fieldCount: 1,
  },
  {
    ticket: 'JO-250005084',
    status: 'successful',
    requestedAt: 'July 12',
    frontliner: 'Angela Reyes',
    fieldCount: 3,
  },
  {
    ticket: 'JO-250005085',
    status: 'for proposal',
    requestedAt: 'July 12',
    frontliner: 'David Lee',
    fieldCount: 2,
  },
  {
    ticket: 'JO-250005083',
    status: 'closed',
    requestedAt: 'July 12',
    frontliner: 'Robert Johnson',
    fieldCount: 1,
  },
  {
    ticket: 'JO-250005084',
    status: 'successful',
    requestedAt: 'July 12',
    frontliner: 'Angela Reyes',
    fieldCount: 3,
  },
  {
    ticket: 'JO-250005085',
    status: 'for proposal',
    requestedAt: 'July 12',
    frontliner: 'David Lee',
    fieldCount: 2,
  },
]
</script>

<template>
  <Card class="shadow transition-shadow hover:shadow-md">
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-primary">
          Awaiting Correction Reviews
        </CardTitle>
        <CardDescription class="text-sm leading-3 text-muted-foreground">
          Most recent ticket correction requests
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent
      class="my-2 max-h-[375px] divide-y divide-border overflow-y-auto"
    >
      <div
        v-for="ticket in data"
        :key="ticket.ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex items-center justify-between">
          <span
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ ticket.ticket }}
          </span>
          <Badge
            variant="outline"
            class="text-xs font-semibold"
          >
            {{ ticket.fieldCount }} changes
          </Badge>
        </div>
        <div class="flex justify-between text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <Clock class="size-3" /> Requested in
            {{ ticket.requestedAt }}
          </span>
          <span>{{ ticket.frontliner }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
