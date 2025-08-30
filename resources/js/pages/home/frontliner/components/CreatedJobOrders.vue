<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { serviceTypes } from '@/constants/service-type'
import { format } from 'date-fns'
import { JobOrderServiceTypeCards } from '../..'

interface CreatedJobOrdersProps {
  data?: JobOrderServiceTypeCards[]
}

defineProps<CreatedJobOrdersProps>()

const serviceTypeBindings = serviceTypes
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">
        Job Order Created
        <span class="pl-2 text-xs font-semibold text-muted-foreground">
          {{ format(new Date(), 'Y') }}
        </span>
      </div>
      <Separator class="flex-1" />
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <Card
        v-for="(statistic, index) in data"
        :key="index"
        class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
      >
        <CardContent class="flex flex-row items-center gap-4 p-5">
          <div
            :class="[
              'flex h-12 w-12 items-center justify-center rounded-full text-white',
              serviceTypeBindings[statistic.serviceType].bgClass,
            ]"
          >
            <component
              :is="serviceTypeBindings[statistic.serviceType].icon"
              class="size-6"
            />
          </div>
          <div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ statistic.serviceType }}
              </p>
              <p
                :class="[
                  'text-2xl font-bold',
                  serviceTypeBindings[statistic.serviceType].textClass,
                ]"
              >
                {{ statistic.total }}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
