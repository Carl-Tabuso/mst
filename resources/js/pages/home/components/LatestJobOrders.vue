<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { serviceTypes } from '@/constants/service-type'
import { format, subMonths } from 'date-fns'
import { computed } from 'vue'
import { JobOrderServiceTypeCards } from '..'

interface LatestJobOrdersProps {
  data?: JobOrderServiceTypeCards[]
}

const props = defineProps<LatestJobOrdersProps>()

const asOfMonth = computed(() => {
  const pastMonth = subMonths(new Date(), 1)
  return format(pastMonth, 'LLLL d')
})

const getTotalForType = (type: string) => {
  const match = props.data?.find((item) => item.serviceType === type)
  return match ? match.total : 0
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
        v-for="(binding, type) in serviceTypes"
        :key="type"
        class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
      >
        <CardContent class="flex items-center gap-4 p-5">
          <div
            :class="[
              'flex h-12 w-12 items-center justify-center rounded-full text-white',
              binding.bgClass,
            ]"
          >
            <component
              :is="binding.icon"
              class="size-6"
            />
          </div>
          <div>
            <p class="text-sm font-medium text-muted-foreground">
              {{ type }}
            </p>
            <p :class="['text-2xl font-bold', binding.textClass]">
              {{ getTotalForType(type) }}
            </p>
            <p class="text-xs text-muted-foreground">as of {{ asOfMonth }}</p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
