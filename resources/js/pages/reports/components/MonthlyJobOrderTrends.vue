<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { LineChart } from '@/components/ui/chart-line'
import { CurveType } from '@unovis/ts'

interface MonthlyJobOrderTrendsProps {
  metrics: {
    month: string
    Completed: number
    Cancelled: number
  }[]
}

defineProps<MonthlyJobOrderTrendsProps>()
</script>

<template>
  <Card class="shadow">
    <CardHeader class="item-center flex flex-col justify-between">
      <CardTitle class="text-lg font-semibold leading-[11px]">
        Monthly Job Order Trends
      </CardTitle>
      <p class="text-sm text-muted-foreground">
        Distribution between completed and cancelled job orders by month.
      </p>
    </CardHeader>
    <CardContent>
      <LineChart
        :data="metrics"
        index="month"
        :categories="['Completed', 'Cancelled']"
        :curve-type="CurveType.Linear"
        class="max-h-[334px] pb-7"
      />
    </CardContent>
  </Card>
</template>
