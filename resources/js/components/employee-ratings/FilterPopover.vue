<script setup lang="ts">
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
import { router } from '@inertiajs/vue3'
import { Calendar, Filter } from 'lucide-vue-next'
import { computed, ref, Ref } from 'vue'

const { routeName, filters: initialFilters } = defineProps<{
  routeName: string
  filters?: {
    statuses?: string[]
    fromDateOfService?: string
    toDateOfService?: string
  }
}>()

const RatingStatuses = [
  { id: 'To be Evaluated', label: 'To be Evaluated' },
  { id: 'Evaluation Done', label: 'Evaluation Done' },
]

interface FilterValues {
  statuses: string[]
  fromDateOfService: string
  toDateOfService: string
}

const filterValues = ref<FilterValues>({
  statuses: initialFilters?.statuses || [],
  fromDateOfService: initialFilters?.fromDateOfService || '',
  toDateOfService: initialFilters?.toDateOfService || '',
}) as Ref

const mainPopoverOpen = ref(false)

const selectedStatuses = computed(() => new Set(filterValues.value.statuses))
const fromDateOfServiceValue = computed(
  () => filterValues.value.fromDateOfService,
)
const toDateOfServiceValue = computed(() => filterValues.value.toDateOfService)

const hasActiveFilters = computed(() => {
  return (
    filterValues.value.statuses.length > 0 ||
    filterValues.value.fromDateOfService ||
    filterValues.value.toDateOfService
  )
})

const applyFilters = () => {
  sendFilterRequest(filterValues.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
  mainPopoverOpen.value = false
}

const clearFilters = () => {
  filterValues.value.statuses = []
  filterValues.value.fromDateOfService = ''
  filterValues.value.toDateOfService = ''

  sendFilterRequest(filterValues.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
  mainPopoverOpen.value = false
}

const handleStatusSelection = (statusId: string, event: boolean) => {
  if (event) {
    if (!filterValues.value.statuses.includes(statusId)) {
      filterValues.value.statuses.push(statusId)
    }
  } else {
    filterValues.value.statuses = filterValues.value.statuses.filter(
      (id: string) => id !== statusId,
    )
  }
}

const handleFromDateClick = () => {
  const input = document.getElementById('fromDateInput') as HTMLInputElement
  if (input) {
    input.showPicker()
  }
}

const handleToDateClick = () => {
  const input = document.getElementById('toDateInput') as HTMLInputElement
  if (input) {
    input.showPicker()
  }
}

const sendFilterRequest = (filterValues: FilterValues, options: object) => {
  const cleanFilters: any = {}

  if (filterValues.statuses.length > 0) {
    cleanFilters.statuses = filterValues.statuses
  }

  if (filterValues.fromDateOfService) {
    cleanFilters.fromDateOfService = filterValues.fromDateOfService
  }

  if (filterValues.toDateOfService) {
    cleanFilters.toDateOfService = filterValues.toDateOfService
  }

  const params: any = {}

  const urlParams = new URLSearchParams(window.location.search)
  const searchParam = urlParams.get('search')
  if (searchParam) {
    params.search = searchParam
  }

  if (Object.keys(cleanFilters).length > 0) {
    params.filters = cleanFilters
  }

  router.get(route(routeName), params, options)
}
</script>

<template>
  <Popover v-model:open="mainPopoverOpen">
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <Filter
          class="mr-2"
          :stroke-width="1"
        />
        Filter
        <template v-if="hasActiveFilters">
          <div class="hidden lg:flex">
            <Badge
              variant="secondary"
              class="rounded-full font-normal"
            >
              {{
                selectedStatuses.size +
                (fromDateOfServiceValue ? 1 : 0) +
                (toDateOfServiceValue ? 1 : 0)
              }}
            </Badge>
          </div>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full"
      align="start"
    >
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Evaluation Status</div>
        <div class="grid grid-cols-1 gap-y-4">
          <div
            v-for="status in RatingStatuses"
            :key="status.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="status.id"
              :checked="selectedStatuses.has(status.id)"
              @update:checked="
                (event) => handleStatusSelection(status.id, event)
              "
              class="border-gray-400 dark:border-white"
            />
            <Label
              :for="status.id"
              class="font-normal"
            >
              {{ status.label }}
            </Label>
          </div>
        </div>
      </div>
      <Separator />
      <div class="my-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Date of Service</div>
        <div class="grid grid-cols-2 gap-10">
          <div class="flex items-center">
            <span class="pr-4 text-sm"> From </span>
            <div
              class="relative cursor-pointer"
              @click="handleFromDateClick"
            >
              <Button
                variant="outline"
                :class="[
                  'w-[200px] ps-3 text-start font-normal',
                  { 'text-muted-foreground': !fromDateOfServiceValue },
                ]"
              >
                <span>
                  {{
                    fromDateOfServiceValue
                      ? new Date(fromDateOfServiceValue).toLocaleDateString(
                          'en-ph',
                          {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                          },
                        )
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
              <input
                id="fromDateInput"
                type="date"
                :value="filterValues.fromDateOfService"
                @input="
                  (e) =>
                    (filterValues.fromDateOfService = (
                      e.target as HTMLInputElement
                    ).value)
                "
                class="absolute inset-0 cursor-pointer opacity-0"
              />
            </div>
          </div>

          <div class="flex items-center">
            <span class="pr-4 text-sm"> To </span>
            <div
              class="relative w-[200px] cursor-pointer"
              @click="handleToDateClick"
            >
              <Button
                variant="outline"
                :class="[
                  'pointer-events-none w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !toDateOfServiceValue },
                ]"
              >
                <span>
                  {{
                    toDateOfServiceValue
                      ? new Date(toDateOfServiceValue).toLocaleDateString(
                          'en-ph',
                          {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                          },
                        )
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
              <input
                id="toDateInput"
                type="date"
                :value="filterValues.toDateOfService"
                @input="
                  (e) =>
                    (filterValues.toDateOfService = (
                      e.target as HTMLInputElement
                    ).value)
                "
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end space-x-2">
        <Button
          @click="clearFilters"
          variant="outline"
          >Clear</Button
        >
        <Button
          @click="applyFilters"
          variant="default"
          >Apply Filter</Button
        >
      </div>
    </PopoverContent>
  </Popover>
</template>
