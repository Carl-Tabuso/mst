<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { MonitorCog, Truck, Wrench } from 'lucide-vue-next'
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">Latest from Job Orders</div>
      <Separator class="flex-1" />
    </div>
    <div class="grid grid-cols-3 gap-4">
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <Truck class="size-14 stroke-1 text-primary will-change-transform" />
          <div>
            <div class="text-sm font-semibold">Waste Management</div>
            <div class="text-xl font-bold text-primary">57</div>
            <div class="text-xs text-muted-foreground">as of July 13</div>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <MonitorCog
            class="size-14 stroke-1 text-primary will-change-transform"
          />
          <div>
            <div class="text-sm font-semibold">IT Service</div>
            <div class="text-xl font-bold text-primary">80</div>
            <div class="text-xs text-muted-foreground">as of July 13</div>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <Wrench class="size-14 stroke-1 text-primary will-change-transform" />
          <div>
            <div class="text-sm font-semibold">Other Service</div>
            <div class="text-xl font-bold text-primary">89</div>
            <div class="text-xs text-muted-foreground">as of 2025</div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
