<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { DonutChart } from '@/components/ui/chart-donut'

interface ServiceTypeTrendsProps {
  data: {
    name: string
    total: number
  }[]
}

defineProps<ServiceTypeTrendsProps>()

// const data = [
//   { name: 'Waste Management', total: Math.floor(Math.random() * 5000 + 1000) },
//   { name: 'IT Services', total: Math.floor(Math.random() * 5000) + 1000 },
//   { name: 'Other Services', total: Math.floor(Math.random() * 5000) + 1000 },
// ]
</script>

<template>
  <Card class="shadow">
    <CardHeader class="item-center flex flex-col justify-between">
      <CardTitle class="text-lg font-semibold leading-[11px]">
        Job Order Service Composition
      </CardTitle>
      <p class="text-sm text-muted-foreground">
        Breakdown of job orders distributed among service types.
      </p>
    </CardHeader>
    <CardContent>
      <DonutChart
        index="name"
        :category="'total'"
        type="pie"
        :data="data"
      />
    </CardContent>
  </Card>
</template>
