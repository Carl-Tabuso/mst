<script setup lang="ts">
import { Truck } from '@/types'
import { Check } from 'lucide-vue-next'

interface TruckPopoverSelectionProps {
  truck: Truck
  isSelected?: boolean
}

withDefaults(defineProps<TruckPopoverSelectionProps>(), {
  isSelected: false,
})
</script>

<template>
  <div class="flex flex-row items-center space-x-3">
    <div
      :class="[
        'flex h-4 w-4 flex-row items-center justify-center rounded-sm border border-primary',
        isSelected
          ? 'bg-primary text-primary-foreground'
          : 'opacity-50 [&_svg]:invisible',
      ]"
    >
      <Check class="h-4 w-4" />
    </div>
    <span class="flex flex-col">
      <span class="text-sm font-medium text-foreground">
        {{ truck.plateNo }}
      </span>
      <span class="text-xs text-muted-foreground">
        {{ truck.model }}
      </span>
    </span>
  </div>
</template>
