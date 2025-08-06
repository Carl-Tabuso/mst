<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  BriefcaseBusiness,
  CalendarHeart,
  ClipboardList,
  Wrench,
} from 'lucide-vue-next'

interface JobOrderStatsGridProps {
  total: number
  top: {
    client: string
    month: string
    service: string
  }
}

defineProps<JobOrderStatsGridProps>()
</script>

<template>
  <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
    <Card class="shadow">
      <CardHeader
        class="item-center flex flex-row justify-between space-y-0 pb-2"
      >
        <CardTitle class="text-sm font-medium text-muted-foreground">
          Total Job Orders
        </CardTitle>
        <ClipboardList class="h-4 w-4 stroke-1 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div class="text-2xl font-bold text-primary">+{{ total }}</div>
        <p class="text-xs text-muted-foreground">Includes all service types</p>
      </CardContent>
    </Card>
    <Card class="shadow">
      <CardHeader
        class="item-center flex flex-row justify-between space-y-0 pb-2"
      >
        <CardTitle class="text-sm font-medium text-muted-foreground">
          Top Business Client
        </CardTitle>
        <BriefcaseBusiness class="h-4 w-4 stroke-1 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div
          :class="[
            'font-bold text-primary sm:text-2xl',
            top.client.length > 12
              ? 'lg:max-w-52 lg:truncate lg:text-lg'
              : 'text-2xl',
          ]"
        >
          {{ top.client }}
        </div>
        <p class="text-xs text-muted-foreground">
          Leading for availing most services
        </p>
      </CardContent>
    </Card>
    <Card class="shadow">
      <CardHeader
        class="item-center flex flex-row justify-between space-y-0 pb-2"
      >
        <CardTitle class="text-sm font-medium text-muted-foreground">
          Peak Month
        </CardTitle>
        <CalendarHeart class="h-4 w-4 stroke-1 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div class="text-2xl font-bold text-primary">
          {{ top.month }}
        </div>
        <p class="text-xs text-muted-foreground">Peak job order month</p>
      </CardContent>
    </Card>
    <Card class="shadow">
      <CardHeader
        class="item-center flex flex-row justify-between space-y-0 pb-2"
      >
        <CardTitle class="text-sm font-medium text-muted-foreground">
          Popular Service
        </CardTitle>
        <Wrench class="h-4 w-4 stroke-1 text-muted-foreground" />
      </CardHeader>
      <CardContent>
        <div
          :class="[
            'font-bold text-primary sm:text-2xl',
            top.client.length > 12
              ? 'lg:max-w-52 lg:truncate lg:text-lg'
              : 'text-2xl',
          ]"
        >
          {{ top.service }}
        </div>
        <p class="text-xs text-muted-foreground">Most availed service type</p>
      </CardContent>
    </Card>
  </div>
</template>
