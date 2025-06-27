<script setup lang="ts">
import { JobOrderStatuses, type JobOrderStatus } from '@/constants/job-order-statuses';
import { ref, computed, Ref } from 'vue';
import { Checkbox } from '@/components/ui/checkbox'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { Calendar } from 'lucide-vue-next'
import AppCalendar from '@/components/AppCalendar.vue'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { DateValue } from 'reka-ui'
import { Filter } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

interface DataTableFacetedFilterProps {
  statuses: JobOrderStatus[]
  fromDateOfService: string
  toDateOfService: string
}

const props = ref<DataTableFacetedFilterProps>({
  statuses: [],
  fromDateOfService: '',
  toDateOfService: '',
}) as Ref

const selectedStatuses = computed(() => new Set(props.value.statuses))
const fromDateOfServiceValue = computed(() => props.value.fromDateOfService)
const toDateOfServiceValue = computed(() => props.value.toDateOfService)

const url = route('job_order.index')

const applyFilters = () => {
  router.get(url, {
    filters: props.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const clearFilters = () => {
  props.value.statuses = []
  props.value.fromDateOfService = ''
  props.value.toDateOfService = ''

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}
  
const handleStatusSelection = (statusId: string, event: boolean) => {
  if (event) {
    if (! props.value.statuses.includes(statusId)) {
      props.value.statuses.push(statusId)
    }
  } else {
    props.value.statuses = props.value.statuses.filter((id: string) => id !== statusId)
  }
}

const handleFromDateOfServiceChange = (newValue: DateValue | undefined) => { 
  props.value.fromDateOfService = newValue?.toString()
}

const handleToDateOfServiceChange = (newValue: DateValue | undefined) => {
  props.value.toDateOfService = newValue?.toString()
}

const formatToDateString = (date: string) => {
  return new Date(date).toLocaleDateString('en-ph', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="ghost" class="ml-1">
        <Filter class="mr-2" :stroke-width="1" />
        Filter
        <template v-if="selectedStatuses.size > 0">
          <div class="hidden lg:flex">
            <Badge
              variant="secondary"
              class="rounded-full font-normal"
            >
              {{ selectedStatuses.size }}
            </Badge>
          </div>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full" align="start">
      <div class="flex flex-col space-y-5 mb-5">
        <div class="font-semibold leading-none text-sm">
          Status
        </div>
        <div class="grid grid-cols-3 gap-y-6 gap-x-10">
          <div v-for="status in JobOrderStatuses" :key="status.id" class="flex items-center gap-x-2">
            <Checkbox
              :id="status.id"
              :checked="selectedStatuses.has(status.id)"
              @update:checked="(event) => handleStatusSelection(status.id, event)"
              class="border-gray-400 dark:border-white" />
            <Label :for="status.id" class="font-normal">
              {{ status.label }}
            </Label>
          </div>                    
        </div>
      </div>
      <Separator />
      <div class="flex flex-col space-y-5 my-5">
        <div class="font-semibold leading-none text-sm">
          Date of Service
        </div>
        <div class="grid grid-cols-2 gap-10">
          <!--From-->
          <div class="flex items-center">
            <span class="pr-4 text-sm">
              From
            </span>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="['w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': ! fromDateOfServiceValue }]">
                  <span>
                    {{ fromDateOfServiceValue ? formatToDateString(fromDateOfServiceValue) : 'Pick a date' }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar
                  :model-value="props.value?.fromDateOfService"
                  @update:model-value="(value) => handleFromDateOfServiceChange(value)" 
                />
              </PopoverContent>
            </Popover>
          </div>
          <!--To-->
          <div class="flex items-center">
            <span class="pr-4 text-sm">
              To
            </span>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="['w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': ! toDateOfServiceValue }]">
                  <span>
                    {{ toDateOfServiceValue ? formatToDateString(toDateOfServiceValue) : 'Pick a date' }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar
                  :model-value="props.value?.toDateOfService"
                  @update:model-value="(value) => handleToDateOfServiceChange(value)" 
                />
              </PopoverContent>
            </Popover>
          </div>          
        </div>
      </div>
      <div class="flex items-center justify-end space-x-2">
        <Button @click="clearFilters" variant="outline">Clear</Button>
        <Button @click="applyFilters" variant="default">Apply Filter</Button>
      </div>
    </PopoverContent>
  </Popover>
</template>
