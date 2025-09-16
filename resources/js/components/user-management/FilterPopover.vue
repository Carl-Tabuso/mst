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

interface FilterPopoverProps {
  routeName?: string
}

interface FilterPopoverEmits {
  (e: 'filter-change', filters: any): void
  (e: 'clear-filters'): void
}

const props = withDefaults(defineProps<FilterPopoverProps>(), {
  routeName: 'users.index',
})

const emit = defineEmits<FilterPopoverEmits>()

const page = usePage()
const initialFilters = page.props.filters || {}
const isParentPopoverOpen = ref(false)

// Status filter options
const statuses = [
  { id: 'active', label: 'Active' },
  { id: 'inactive', label: 'Inactive' },
]

// Local state for filters
const selectedStatuses = ref<string[]>(initialFilters.status ?? [])
const fromDateCreated = ref<DateValue | undefined>(
  initialFilters.fromDateCreated ? new Date(initialFilters.fromDateCreated) : undefined,
)
const toDateCreated = ref<DateValue | undefined>(
  initialFilters.toDateCreated ? new Date(initialFilters.toDateCreated) : undefined,
)

const isCalendarOpen = ref<Record<string, boolean>>({
  from: false,
  to: false,
})

// Handle status selection
const handleStatusSelection = (statusId: string, checked: boolean) => {
  if (checked) {
    if (!selectedStatuses.value.includes(statusId)) {
      selectedStatuses.value.push(statusId)
    }
  } else {
    selectedStatuses.value = selectedStatuses.value.filter((id) => id !== statusId)
  }
}

// Format date for display
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

// Serialize date for API
const serializeDate = (date: DateValue | undefined) => {
  if (!date) return ''
  if (typeof (date as any).toDate === 'function') {
    return (date as any).toDate().toISOString()
  }
  return new Date(String(date)).toISOString()
}

// Apply filters
const applyFilters = () => {
  isParentPopoverOpen.value = false
  emit('filter-change', {
    status: selectedStatuses.value.length > 0 ? selectedStatuses.value : null,
    fromDateCreated: serializeDate(fromDateCreated.value),
    toDateCreated: serializeDate(toDateCreated.value),
  })
}

// Clear filters
const clearFilters = () => {
  selectedStatuses.value = []
  fromDateCreated.value = undefined
  toDateCreated.value = undefined
  isParentPopoverOpen.value = false
  emit('clear-filters')
}

// Count selected filters for badge
const selectedFilterCount = computed(() => {
  let count = selectedStatuses.value.length
  if (fromDateCreated.value) count++
  if (toDateCreated.value) count++
  return count
})
</script>

<template>
  <Popover v-model:open="isParentPopoverOpen">
    <PopoverTrigger as-child>
      <slot />
    </PopoverTrigger>
    <PopoverContent class="w-96" align="start">
      <!-- Status Filter -->
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Account Status</div>
        <div class="grid grid-cols-2 gap-y-4">
          <div
            v-for="status in statuses"
            :key="status.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="status.id"
              :checked="selectedStatuses.includes(status.id)"
              @update:checked="(checked) => handleStatusSelection(status.id, checked)"
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

      <!-- Date Created Filter -->
      <div class="my-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">
          Account Created Date
        </div>
        <div class="grid grid-cols-2 gap-4">
          <!-- From Date -->
          <div class="flex flex-col space-y-2">
            <Label class="text-sm">From</Label>
            <Popover v-model:open="isCalendarOpen.from">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-full ps-3 text-start font-normal',
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

          <!-- To Date -->
          <div class="flex flex-col space-y-2">
            <Label class="text-sm">To</Label>
            <Popover v-model:open="isCalendarOpen.to">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[
                    'w-full ps-3 text-start font-normal',
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

      <Separator />

      <!-- Actions -->
      <div class="flex items-center justify-end space-x-2 pt-4">
        <Button
          @click="clearFilters"
          variant="outline"
          size="sm"
        >
          Clear All
        </Button>
        <Button
          @click="applyFilters"
          variant="default"
          size="sm"
        >
          Apply Filters
        </Button>
      </div>
    </PopoverContent>
  </Popover>
</template>