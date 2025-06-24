<script setup lang="ts">
import { computed } from 'vue'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { Badge } from '@/components/ui/badge'
import { Calendar } from 'lucide-vue-next'
import AppCalendar from '@/components/AppCalendar.vue'
import { JobOrderStatuses, type JobOrderStatus } from '@/constants/job-order-statuses'
import type { DateValue } from 'reka-ui'
import { Filter } from 'lucide-vue-next'

interface DataTableFilters {
  statuses: JobOrderStatus[],
  fromDOS: string,
  toDOS: string
}

const props = defineProps<{
    filters: DataTableFilters
}>()

const emit = defineEmits<{
    (e: 'update:filters', value: DataTableFilters): void
    (e: 'apply'): void
    (e: 'clear'): void
}>()

const selected = computed(() => new Set(props.filters.statuses))
const fromDOSValue = computed(() => props.filters.fromDOS)
const toDOSValue = computed(() => props.filters.toDOS)

const handleStatusSelection = (id: JobOrderStatus, checked: boolean) => {
    const newStatuses = checked
        ? [...props.filters.statuses, id]
        : props.filters.statuses.filter((s) => s !== id)

    emit('update:filters', { ...props.filters, statuses: newStatuses })
}

const handleDateChange = (field: 'fromDOS'|'toDOS', value: DateValue|undefined) => {
    // console.log(value)
    emit('update:filters', {
        ...props.filters,
        [field]: value?.toString()
    })
}
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="ghost" class="ml-1">
        <Filter class="mr-2" :stroke-width="1" />
        Filter
        <template v-if="selected.size > 0">
          <div class="hidden lg:flex">
            <Badge variant="secondary" class="rounded-full font-normal">
              {{ selected.size }}
            </Badge>
          </div>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full" align="start">
      <div class="flex flex-col space-y-5 mb-5">
        <div class="font-semibold leading-none text-sm">Status</div>
        <div class="grid grid-cols-3 gap-y-6 gap-x-10">
          <div
            v-for="status in JobOrderStatuses"
            :key="status.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="status.id"
              :checked="selected.has(status.id)"
              @update:checked="(checked) => handleStatusSelection(status.id, checked)"
              class="border-gray-400 dark:border-white"
            />
            <Label :for="status.id" class="font-normal">
              {{ status.label }}
            </Label>
          </div>
        </div>
      </div>

      <Separator />

      <div class="flex flex-col space-y-5 my-5">
        <div class="font-semibold leading-none text-sm">Date of Service</div>
        <div class="grid grid-cols-2 gap-10">
          <div class="flex items-center">
            <span class="pr-4 text-sm">From</span>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="['w-[240px] ps-3 text-start font-normal', ! fromDOSValue ? 'text-muted-foreground' : '']"
                >
                  <span>
                    {{
                      fromDOSValue
                        ? new Date(fromDOSValue).toLocaleDateString('en-ph', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          })
                        : 'Pick a date'
                    }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar
                  :model-value="props.filters?.fromDOS"
                  @update:model-value="(val) => handleDateChange('fromDOS', val)"
                />
              </PopoverContent>
            </Popover>
          </div>

          <div class="flex items-center">
            <span class="pr-4 text-sm">To</span>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="['w-[240px] ps-3 text-start font-normal', ! toDOSValue  ? 'text-muted-foreground' : '']"
                >
                  <span>
                    {{
                      toDOSValue 
                        ? new Date(toDOSValue ).toLocaleDateString('en-ph', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          })
                        : 'Pick a date'
                    }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar
                  :model-value="props.filters?.toDOS"
                  @update:model-value="(val) => handleDateChange('toDOS', val)"
                />
              </PopoverContent>
            </Popover>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end space-x-2">
        <Button @click="emit('clear')" variant="outline">Clear</Button>
        <Button @click="emit('apply')" variant="default">Apply Filter</Button>
      </div>
    </PopoverContent>
  </Popover>
</template>
