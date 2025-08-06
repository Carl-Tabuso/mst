<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { BarChart } from '@/components/ui/chart-bar'

const data = [
  { name: 'Waste Management', Completed: 17, Cancelled: 8 },
  { name: 'IT Services', Completed: 10, Cancelled: 4 },
  { name: 'Other Services', Completed: 8, Cancelled: 15 },
]
</script>

<template>
  <Card class="shadow">
    <CardHeader class="item-center flex flex-col justify-between">
      <CardTitle class="text-lg font-semibold leading-[11px]">
        Job Order Service Breakdown
      </CardTitle>
      <p class="text-sm text-muted-foreground">
        Comparison of completed and cancelled job orders across service types.
      </p>
    </CardHeader>
    <CardContent>
      <BarChart
        :data="data"
        index="name"
        :categories="['Completed', 'Cancelled']"
        :rounded-corners="5"
        class="h-48"
        :show-legend="false"
      />
    </CardContent>
  </Card>
</template>
