<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { RangeCalendar } from '@/components/ui/range-calendar'
import { format } from 'date-fns'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'
import { useJobOrderTable } from '../composables/useJobOrderTable'

const { dataTable, onDateOfServiceRangePick } = useJobOrderTable()

const dateOfServiceRange = computed(() => {
  const dateRange = dataTable.dateOfServiceRange.value
  return {
    start: dateRange.start,
    end: dateRange.end,
  }
})

const hasStartAndEndDate = computed(() => {
  return dateOfServiceRange.value.start && dateOfServiceRange.value.end
})

const buttonPlaceholder = computed(() => {
  const dateOfService = dateOfServiceRange.value
  return hasStartAndEndDate.value
    ? `${format(dateOfService!.start!.toString(), 'MMM dd, yyy')} - ${format(dateOfService!.end!.toString(), 'MMM dd, yyy')}`
    : 'Pick a date'
})
</script>

<template>
  <div class="flex flex-row items-center gap-4">
    <Label class="w-28 shrink-0 text-sm leading-none"> Date of Service </Label>
    <Popover>
      <PopoverTrigger as-child>
        <Button
          variant="outline"
          class="w-full justify-start text-left font-normal"
          :class="[
            hasStartAndEndDate ? 'text-foreground' : 'text-muted-foreground',
          ]"
        >
          <Calendar class="mr-2 h-4 w-4" />
          <span>
            {{ buttonPlaceholder }}
          </span>
        </Button>
      </PopoverTrigger>
      <PopoverContent
        class="w-auto p-0"
        align="end"
      >
        <RangeCalendar
          :model-value="dataTable.dateOfServiceRange as any"
          @update:model-value="onDateOfServiceRangePick"
          weekday-format="short"
          :number-of-months="2"
          initial-focus
        />
      </PopoverContent>
    </Popover>
  </div>
</template>
