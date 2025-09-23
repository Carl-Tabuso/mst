<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { Filter } from 'lucide-vue-next'
import { useArchivedJobOrderTable } from '../../composables/useArchivedJobOrderTable'
import DateOfArchivedFilter from './DateOfArchivedFilter.vue'
import DateOfServiceFilter from './DateOfServiceFilter.vue'

const { dataTable, onStatusSelect, applyFilters, clearFilters } =
  useArchivedJobOrderTable()

const { statusMap } = useJobOrderDicts()
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <Filter class="mr-2" />
        Filter
        <Badge
          v-if="dataTable.statuses.value.length"
          variant="secondary"
          class="rounded-full font-extrabold"
        >
          {{ dataTable.statuses.value.length }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full"
      align="start"
    >
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-medium leading-none">Status</div>
        <div class="grid grid-cols-3 gap-x-10 gap-y-5">
          <div
            v-for="{ id, label } in JobOrderStatuses"
            :key="id"
            class="flex flex-row items-center gap-x-2"
          >
            <Checkbox
              :id="id"
              :checked="dataTable.statuses.value.includes(id)"
              @update:checked="(event) => onStatusSelect(id, event)"
            />
            <Label :for="id">
              <Badge
                :variant="statusMap[id].badge"
                class="border-0 p-0"
              >
                {{ label }}
              </Badge>
            </Label>
          </div>
        </div>
      </div>
      <Separator />
      <div class="my-4 flex flex-col space-y-4">
        <DateOfServiceFilter />
        <DateOfArchivedFilter />
      </div>
      <div class="flex items-center justify-end space-x-2">
        <Button
          variant="outline"
          @click="clearFilters"
        >
          Clear
        </Button>
        <Button @click="applyFilters"> Apply Filters </Button>
      </div>
    </PopoverContent>
  </Popover>
</template>
