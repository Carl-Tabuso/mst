<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { BarChart } from '@/components/ui/chart-bar'
import { CurrentYearParticipation } from '..'

interface ParticipationOverViewProps {
  data?: CurrentYearParticipation[]
}

defineProps<ParticipationOverViewProps>()
</script>

<template>
  <Card class="shadow">
    <CardHeader class="item-center flex flex-col justify-between">
      <CardTitle class="text-lg font-semibold leading-[11px] text-foreground">
        Participation by Service Type
      </CardTitle>
      <p class="text-sm text-muted-foreground">
        See how your job orders are distributed across different services.
      </p>
    </CardHeader>
    <CardContent>
      <BarChart
        v-if="data?.length"
        :data="data"
        index="month"
        :categories="['Waste Management', 'IT Service', 'Other Services']"
        :rounded-corners="5"
        class="h-[270px] pb-7"
      />
    </CardContent>
  </Card>
</template>
