<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { RangeCalendar } from '@/components/ui/range-calendar'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'
import { useArchivedTrucksTable } from '../../composables/useArchivedTrucksTable'

const { dataTable, onDateArchivedRangePick, onClearDateArchivedRange } =
  useArchivedTrucksTable()

const hasStartAndEndDate = computed(() => {
  const dateArchived = dataTable.dateArchived.value
  return dateArchived.start && dateArchived.end
})
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
        :class="{ 'font-semibold text-foreground': hasStartAndEndDate }"
      >
        <Calendar class="mr-2" />
        Archival
        <Badge
          v-if="hasStartAndEndDate"
          class="h-2 w-2 rounded-full p-0"
        />
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full p-0"
      align="start"
    >
      <div class="mb-4 flex flex-col">
        <RangeCalendar
          :model-value="dataTable.dateArchived as any"
          @update:model-value="onDateArchivedRangePick"
          weekday-format="short"
          :number-of-months="2"
          initial-focus
        />
        <Button
          v-if="hasStartAndEndDate"
          variant="secondary"
          class="mx-5"
          @click="onClearDateArchivedRange"
        >
          Clear Filters
        </Button>
      </div>
    </PopoverContent>
  </Popover>
</template>
