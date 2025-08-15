<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { ItServiceStatuses, type ItServiceStatus } from '@/constants/it-service-statuses'
import { router } from '@inertiajs/vue3'
import { Calendar, Filter } from 'lucide-vue-next'
import { DateValue } from 'reka-ui'
import { computed, ref, Ref } from 'vue'

const { routeName } = defineProps<{
  routeName: string
}>()

interface DataTableFacetedFilterProps {
  itServiceStatuses: ItServiceStatus[]
  fromDateOfService: string
  toDateOfService: string
}

const props = ref<DataTableFacetedFilterProps>({
  itServiceStatuses: [],
  fromDateOfService: '',
  toDateOfService: '',
}) as Ref

const selectedStatuses = computed(() => new Set(props.value.itServiceStatuses))
const fromDateOfServiceValue = computed(() => props.value.fromDateOfService)
const toDateOfServiceValue = computed(() => props.value.toDateOfService)

const applyFilters = () => {
  sendFilterRequest(props.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const clearFilters = () => {
  props.value.itServiceStatuses = []
  props.value.fromDateOfService = ''
  props.value.toDateOfService = ''

  sendFilterRequest(props.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const handleStatusSelection = (statusId: string, event: boolean) => {
  if (event) {
    if (!props.value.itServiceStatuses.includes(statusId)) {
      props.value.itServiceStatuses.push(statusId)
    }
  } else {
    props.value.itServiceStatuses = props.value.itServiceStatuses.filter(
      (id: string) => id !== statusId,
    )
  }
}

const handleFromDateOfServiceChange = (newValue: DateValue | undefined) => {
  props.value.fromDateOfService = newValue?.toString()
}

const handleToDateOfServiceChange = (newValue: DateValue | undefined) => {
  props.value.toDateOfService = newValue?.toString()
}

const sendFilterRequest = (filterValues: any, options: object) => {
  router.get(route(routeName), { filters: filterValues }, options)
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
            <Badge variant="secondary" class="rounded-full font-normal">
              {{ selectedStatuses.size }}
            </Badge>
          </div>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full" align="start">
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">IT Service Status</div>
        <div class="grid grid-cols-2 gap-x-6 gap-y-4">
          <div v-for="status in ItServiceStatuses" :key="status.id" class="flex items-center gap-x-2">
            <Checkbox :id="status.id" :checked="selectedStatuses.has(status.id)" @update:checked="
              (event) => handleStatusSelection(status.id, event)
            " class="border-gray-400 dark:border-white" />
            <Label :for="status.id" class="font-normal text-sm">
              <Badge :variant="status.badge" class="text-xs">
                {{ status.label }}
              </Badge>
            </Label>
          </div>
        </div>
      </div>
      <Separator />
      <div class="my-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Date of Service</div>
        <div class="grid grid-cols-1 gap-4">
          <!--From-->
          <div class="flex items-center">
            <span class="pr-4 text-sm min-w-[50px]"> From </span>
            <Popover>
              <PopoverTrigger as-child>
                <Button variant="outline" :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !fromDateOfServiceValue },
                ]">
                  <span>
                    {{
                      fromDateOfServiceValue
                        ? new Date(fromDateOfServiceValue).toLocaleDateString(
                          'en-ph',
                          {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          },
                        )
                        : 'Pick start date'
                    }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar :model-value="props.value?.fromDateOfService" @update:model-value="
                  (value) => handleFromDateOfServiceChange(value)
                " />
              </PopoverContent>
            </Popover>
          </div>
          <!--To-->
          <div class="flex items-center">
            <span class="pr-4 text-sm min-w-[50px]"> To </span>
            <Popover>
              <PopoverTrigger as-child>
                <Button variant="outline" :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !toDateOfServiceValue },
                ]">
                  <span>
                    {{
                      toDateOfServiceValue
                        ? new Date(toDateOfServiceValue).toLocaleDateString(
                          'en-ph',
                          {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          },
                        )
                        : 'Pick end date'
                    }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar :model-value="props.value?.toDateOfService" @update:model-value="
                  (value) => handleToDateOfServiceChange(value)
                " />
              </PopoverContent>
            </Popover>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end space-x-2 pt-4">
        <Button @click="clearFilters" variant="outline" size="sm">Clear Filters</Button>
        <Button @click="applyFilters" variant="default" size="sm">Apply Filters</Button>
      </div>
    </PopoverContent>
  </Popover>
</template>