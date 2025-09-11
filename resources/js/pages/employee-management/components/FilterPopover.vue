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
import { computed, ref } from 'vue'

interface DataTableFacetedFilterProps {
  routeName?: string
}

const props = withDefaults(defineProps<DataTableFacetedFilterProps>(), {
  routeName: 'employee-management.index',
})

const page = usePage()
const initialFilters = page.props.filters || {}

const accountStatuses = [
  { id: 'active', label: 'Active' },
  { id: 'deactivated', label: 'Deactivated' },
  { id: 'no_account', label: 'No Account' },
]

const statuses = ref<string[]>(initialFilters.statuses ?? [])

const fromDateCreated = ref<DateValue | undefined>(
  initialFilters.from ? new Date(initialFilters.from) : undefined,
)

const toDateCreated = ref<DateValue | undefined>(
  initialFilters.to ? new Date(initialFilters.to) : undefined,
)

const selectedStatuses = computed(() => new Set(statuses.value))
const url = computed(() => route(props.routeName))
const isParentPopoverOpen = ref(false)
const isCalendarOpen = ref<Record<string, boolean>>({
  from: false,
  to: false,
})

const handleStatusSelection = (statusId: string, checked: boolean) => {
  if (checked) {
    if (!statuses.value.includes(statusId)) {
      statuses.value.push(statusId)
    }
  } else {
    statuses.value = statuses.value.filter((id) => id !== statusId)
  }
}

const formatToDateString = (date: DateValue | undefined) => {
  if (!date) return ''
  if (typeof (date as any).toDate === 'function') {
    return (date as any).toDate().toLocaleDateString('en-PH', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  }
  return new Date(String(date)).toLocaleDateString('en-PH', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const serializeDate = (date: DateValue | undefined) => {
  if (!date) return ''
  if (typeof (date as any).toDate === 'function') {
    return (date as any).toDate().toISOString()
  }
  return new Date(String(date)).toISOString()
}

const applyFilters = () => {
  isParentPopoverOpen.value = false

  router.get(
    url.value,
    {
      filters: {
        accountStatuses: statuses.value,
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
  fromDateCreated.value = undefined
  toDateCreated.value = undefined

  isParentPopoverOpen.value = false

  router.get(
    url.value,
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}
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
    <PopoverContent
      class="w-full"
      align="start"
    >
      <!-- Status Filter -->
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

      <!-- Date Filter -->
      <div class="my-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">
          Account Created Date
        </div>
        <div class="grid grid-cols-2 gap-10">
          <!-- From -->
          <div class="flex items-center">
            <span class="pr-4 text-sm"> From </span>
            <Popover v-model:open="isCalendarOpen.from">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': !fromDateCreated },
                  ]"
                >
                  <span>
                    {{ formatToDateString(fromDateCreated) || 'Pick a date' }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar v-model="fromDateCreated" />
              </PopoverContent>
            </Popover>
          </div>

          <!-- To -->
          <div class="flex items-center">
            <span class="pr-4 text-sm"> To </span>
            <Popover v-model:open="isCalendarOpen.to">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-[240px] ps-3 text-start font-normal',
                    { 'text-muted-foreground': !toDateCreated },
                  ]"
                >
                  <span>
                    {{ formatToDateString(toDateCreated) || 'Pick a date' }}
                  </span>
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

      <!-- Actions -->
      <div class="flex items-center justify-end space-x-2">
        <Button
          @click="clearFilters"
          variant="outline"
          size="sm"
          >Clear</Button
        >
        <Button
          @click="applyFilters"
          variant="default"
          size="sm"
          >Apply Filter</Button
        >
      </div>
    </PopoverContent>
  </Popover>
</template>
