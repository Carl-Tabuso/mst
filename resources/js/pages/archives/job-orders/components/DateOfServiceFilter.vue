<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { RangeCalendar } from '@/components/ui/range-calendar'
import { CalendarDate } from '@internationalized/date'
import { format } from 'date-fns'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'
import { useArchivedJobOrderTable } from '../../composables/useArchivedJobOrderTable'

const { dataTable, onDateOfServiceRangePick } = useArchivedJobOrderTable()

const date = new Date()
const placeholderDate = new CalendarDate(
  date.getFullYear(),
  date.getMonth() + 1,
  date.getDate(),
)

const dateOfServiceRange = computed(() => {
  const dateRange = dataTable.dateOfServiceRange.value
  return {
    start: dateRange.start,
    end: dateRange.end,
  }
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
            dateOfServiceRange.start && dateOfServiceRange.end
              ? 'text-foreground'
              : 'text-muted-foreground',
          ]"
        >
          <Calendar class="mr-2 h-4 w-4" />
          <span>
            {{
              dateOfServiceRange.start && dateOfServiceRange.end
                ? `${format(dateOfServiceRange.start.toString(), 'MMM dd, yyy')} - ${format(dateOfServiceRange.end.toString(), 'MMM dd, yyy')}`
                : 'Pick a date range'
            }}
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
          :placeholder="placeholderDate"
        />
      </PopoverContent>
    </Popover>
  </div>
</template>
