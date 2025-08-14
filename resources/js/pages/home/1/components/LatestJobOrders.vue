<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { ServiceType } from '@/types'
import { format, subMonths } from 'date-fns'
import { MonitorCog, Truck, Wrench } from 'lucide-vue-next'
import { computed } from 'vue'
import { JobOrderServiceTypeCards } from '..'

interface LatestJobOrdersProps {
  data?: JobOrderServiceTypeCards[]
}

defineProps<LatestJobOrdersProps>()

const serviceTypeBindings: Record<ServiceType, any> = {
  'Waste Management': {
    icon: Truck,
    bgClass: 'bg-tertiary/75 text-white',
    textClass: 'text-tertiary',
  },
  'IT Service': {
    icon: MonitorCog,
    bgClass: 'bg-sky-900/75 text-white',
    textClass: 'text-sky-900',
  },
  'Other Services': {
    icon: Wrench,
    bgClass: 'bg-zinc-700/75 text-white',
    textClass: 'text-muted-foreground',
  },
}

const asOfMonth = computed(() => {
  const pastMonth = subMonths(new Date(), 1)
  return format(pastMonth, 'LLLL d')
})

const getIcon = (servceType: ServiceType) => {
  return serviceTypeBindings[servceType].icon
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">Latest from Job Orders</div>
      <Separator class="flex-1" />
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <Card
        v-for="(latest, index) in data"
        :key="index"
        class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
      >
        <CardContent class="flex items-center gap-4 p-5">
          <div
            :class="[
              'flex h-12 w-12 items-center justify-center rounded-full text-white',
              serviceTypeBindings[latest.serviceType].bgClass,
            ]"
          >
            <component
              :is="getIcon(latest.serviceType)"
              class="size-6"
            />
          </div>
          <div>
            <p class="text-sm font-medium text-muted-foreground">
              {{ latest.serviceType }}
            </p>
            <p
              :class="[
                'text-2xl font-bold',
                serviceTypeBindings[latest.serviceType].textClass,
              ]"
            >
              {{ latest.total }}
            </p>
            <p class="text-xs text-muted-foreground">as of {{ asOfMonth }}</p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
