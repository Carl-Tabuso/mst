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
import { router, usePage } from '@inertiajs/vue3'
import { Calendar, Filter } from 'lucide-vue-next'
import type { DateValue } from 'reka-ui'
import { computed, ref, watch } from 'vue'

interface DataTableFacetedFilterProps {
  routeName?: string
  positions?: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<DataTableFacetedFilterProps>(), {
  routeName: 'employee-management.index',
  positions: () => [],
})

const page = usePage()

const accountStatuses = Object.freeze([
  { id: 'active', label: 'Active' },
  { id: 'deactivated', label: 'Deactivated' },
  { id: 'no_account', label: 'No Account' },
])

const statuses = ref<string[]>([])
const selectedPositions = ref<number[]>([])
const fromDateCreated = ref<DateValue | undefined>()
const toDateCreated = ref<DateValue | undefined>()
const isParentPopoverOpen = ref(false)
const isCalendarOpen = ref({ from: false, to: false })

const selectedStatuses = computed(() => new Set(statuses.value))
const activeFilterCount = computed(
  () =>
    selectedStatuses.value.size +
    selectedPositions.value.length +
    (fromDateCreated.value ? 1 : 0) +
    (toDateCreated.value ? 1 : 0),
)

const formatToDateString = (date: DateValue | undefined) => {
  if (!date) return ''
  const dateObj =
    typeof (date as any).toDate === 'function'
      ? (date as any).toDate()
      : new Date(String(date))
  return dateObj.toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const serializeDate = (date: DateValue | undefined) => {
  if (!date) return ''
  const dateObj =
    typeof (date as any).toDate === 'function'
      ? (date as any).toDate()
      : new Date(String(date))
  return dateObj.toISOString()
}

const updateFiltersFromUrl = () => {
  const currentFilters = page.props.filters || {}

  statuses.value = currentFilters.accountStatuses || []
  selectedPositions.value = (currentFilters.positions || []).map(Number)
  fromDateCreated.value = currentFilters.fromDateCreated
    ? new Date(currentFilters.fromDateCreated)
    : undefined
  toDateCreated.value = currentFilters.toDateCreated
    ? new Date(currentFilters.toDateCreated)
    : undefined
}

updateFiltersFromUrl()

watch(
  () => page.props.filters,
  () => {
    updateFiltersFromUrl()
  },
  { deep: true },
)

const handleStatusSelection = (statusId: string, checked: boolean) => {
  if (checked) {
    statuses.value = [...statuses.value, statusId]
  } else {
    statuses.value = statuses.value.filter((id) => id !== statusId)
  }
}

const handlePositionSelection = (positionId: number, checked: boolean) => {
  if (checked) {
    selectedPositions.value = [...selectedPositions.value, positionId]
  } else {
    selectedPositions.value = selectedPositions.value.filter(
      (id) => id !== positionId,
    )
  }
}

const applyFilters = () => {
  isParentPopoverOpen.value = false
  router.get(
    route(props.routeName),
    {
      filters: {
        accountStatuses: statuses.value,
        positions: selectedPositions.value,
        fromDateCreated: serializeDate(fromDateCreated.value),
        toDateCreated: serializeDate(toDateCreated.value),
      },
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

const clearFilters = () => {
  statuses.value = []
  selectedPositions.value = []
  fromDateCreated.value = undefined
  toDateCreated.value = undefined
  isParentPopoverOpen.value = false

  router.get(
    route(props.routeName),
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

watch(isParentPopoverOpen, (isOpen) => {
  if (isOpen) {
    updateFiltersFromUrl()
  }
})
</script>

<template>
  <Popover v-model:open="isParentPopoverOpen">
    <PopoverTrigger as-child>
      <Button
        variant="ghost"
        class="ml-1"
      >
        <Filter class="mr-2 size-4" />
        Filter
        <Badge
          v-if="activeFilterCount > 0"
          variant="secondary"
          class="ml-1 hidden rounded-full font-normal lg:flex"
        >
          {{ activeFilterCount }}
        </Badge>
      </Button>
    </PopoverTrigger>
    <PopoverContent
      class="w-full"
      align="start"
    >
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Account Status</div>
        <div class="grid grid-cols-2 gap-x-10 gap-y-4">
          <div
            v-for="status in accountStatuses"
            :key="status.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="status.id"
              :checked="selectedStatuses.has(status.id)"
              @update:checked="
                (checked) => handleStatusSelection(status.id, checked)
              "
              class="border-gray-400 dark:border-white"
            />
            <Label
              :for="status.id"
              class="text-sm font-normal"
            >
              {{ status.label }}
            </Label>
          </div>
        </div>
      </div>

      <Separator />

      <div
        v-if="positions.length > 0"
        class="my-5 flex flex-col space-y-5"
      >
        <div class="text-sm font-semibold leading-none">Position</div>
        <div class="grid grid-cols-2 gap-x-10 gap-y-4">
          <div
            v-for="position in positions"
            :key="position.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="`position-${position.id}`"
              :checked="selectedPositions.includes(position.id)"
              @update:checked="
                (checked) => handlePositionSelection(position.id, checked)
              "
              class="border-gray-400 dark:border-white"
            />
            <Label
              :for="`position-${position.id}`"
              class="text-sm font-normal"
            >
              {{ position.name }}
            </Label>
          </div>
        </div>
      </div>

      <Separator />

      <div class="my-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">
          Account Created Date
        </div>
        <div class="grid grid-cols-2 gap-10">
          <div class="flex items-center">
            <span class="pr-4 text-sm">From</span>
            <Popover v-model:open="isCalendarOpen.from">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': !fromDateCreated },
                  ]"
                >
                  <span>{{
                    formatToDateString(fromDateCreated) || 'Pick a date'
                  }}</span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar v-model="fromDateCreated" />
              </PopoverContent>
            </Popover>
          </div>

          <div class="flex items-center">
            <span class="pr-4 text-sm">To</span>
            <Popover v-model:open="isCalendarOpen.to">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': !toDateCreated },
                  ]"
                >
                  <span>{{
                    formatToDateString(toDateCreated) || 'Pick a date'
                  }}</span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar v-model="toDateCreated" />
              </PopoverContent>
            </Popover>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end space-x-2">
        <Button
          @click="clearFilters"
          variant="outline"
          size="sm"
        >
          Clear
        </Button>
        <Button
          @click="applyFilters"
          variant="default"
          size="sm"
        >
          Apply Filter
        </Button>
      </div>
    </PopoverContent>
  </Popover>
</template>
