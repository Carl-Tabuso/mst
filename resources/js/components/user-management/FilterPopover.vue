<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { Calendar } from 'lucide-vue-next'
import type { DateValue } from 'reka-ui'
import { ref, watch } from 'vue'

interface FilterPopoverProps {
  routeName?: string
  roles?: Array<{ id: number; name: string }>
  currentFilters?: Record<string, any>
}

interface FilterPopoverEmits {
  (e: 'filter-change', filters: any): void
  (e: 'clear-filters'): void
}

const props = withDefaults(defineProps<FilterPopoverProps>(), {
  routeName: 'users.index',
  roles: () => [],
  currentFilters: () => ({})
})

const emit = defineEmits<FilterPopoverEmits>()

const isParentPopoverOpen = ref(false)
const isCalendarOpen = ref({ from: false, to: false })

const localSelectedRoles = ref<number[]>([])
const localFromDateCreated = ref<DateValue | undefined>(undefined)
const localToDateCreated = ref<DateValue | undefined>(undefined)

watch(() => props.currentFilters, (newFilters) => {
  localSelectedRoles.value = Array.isArray(newFilters.role) 
    ? newFilters.role.map(role => Number(role)) 
    : []
  
  localFromDateCreated.value = newFilters.fromDateCreated 
    ? new Date(newFilters.fromDateCreated) 
    : undefined
  
  localToDateCreated.value = newFilters.toDateCreated 
    ? new Date(newFilters.toDateCreated) 
    : undefined
}, { deep: true, immediate: true })

const handleRoleSelection = (roleId: number, checked: boolean) => {
  if (checked) {
    if (!localSelectedRoles.value.includes(roleId)) {
      localSelectedRoles.value.push(roleId)
    }
  } else {
    localSelectedRoles.value = localSelectedRoles.value.filter(id => id !== roleId)
  }
}

const formatToDateString = (date: DateValue | undefined) => {
  if (!date) return ''
  
  const dateObj = typeof (date as any).toDate === 'function' 
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
  
  const dateObj = typeof (date as any).toDate === 'function' 
    ? (date as any).toDate() 
    : new Date(String(date))
  
  return dateObj.toISOString()
}

const applyFilters = () => {
  isParentPopoverOpen.value = false
  
  const filters: any = {}
  
  if (localSelectedRoles.value.length > 0) {
    filters.role = localSelectedRoles.value
  } else {
    filters.role = []
  }
  
  const fromDate = serializeDate(localFromDateCreated.value)
  const toDate = serializeDate(localToDateCreated.value)
  
  if (fromDate) {
    filters.fromDateCreated = fromDate
  } else {
    filters.fromDateCreated = ''
  }
  
  if (toDate) {
    filters.toDateCreated = toDate
  } else {
    filters.toDateCreated = ''
  }
  
  emit('filter-change', filters)
}

const clearFilters = () => {
  localSelectedRoles.value = []
  localFromDateCreated.value = undefined
  localToDateCreated.value = undefined
  isParentPopoverOpen.value = false
  
  emit('clear-filters')
}

const resetLocalState = () => {
  localSelectedRoles.value = Array.isArray(props.currentFilters.role) 
    ? props.currentFilters.role.map(role => Number(role)) 
    : []
  
  localFromDateCreated.value = props.currentFilters.fromDateCreated 
    ? new Date(props.currentFilters.fromDateCreated) 
    : undefined
  
  localToDateCreated.value = props.currentFilters.toDateCreated 
    ? new Date(props.currentFilters.toDateCreated) 
    : undefined
}
</script>

<template>
  <Popover v-model:open="isParentPopoverOpen" @open-auto="resetLocalState">
    <PopoverTrigger as-child>
      <slot />
    </PopoverTrigger>
    <PopoverContent class="w-96" align="start">
      <!-- Role Filter -->
      <div class="mb-5 flex flex-col space-y-5">
        <div class="text-sm font-semibold leading-none">Role</div>
        <div class="grid grid-cols-3 gap-x-4 gap-y-3">
          <div
            v-for="role in roles"
            :key="role.id"
            class="flex items-center gap-x-2"
          >
            <Checkbox
              :id="`role-${role.id}`"
              :checked="localSelectedRoles.includes(role.id)"
              @update:checked="(checked) => handleRoleSelection(role.id, checked)"
              class="border-gray-400 dark:border-white"
            />
            <Label
              :for="`role-${role.id}`"
              class="text-sm font-normal truncate"
              :title="role.name"
            >
              {{ role.name }}
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
                    { 'text-muted-foreground': !localFromDateCreated },
                  ]"
                >
                  <span>
                    {{ formatToDateString(localFromDateCreated) || 'Pick a date' }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar v-model="localFromDateCreated" />
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
                    { 'text-muted-foreground': !localToDateCreated },
                  ]"
                >
                  <span>
                    {{ formatToDateString(localToDateCreated) || 'Pick a date' }}
                  </span>
                  <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <AppCalendar v-model="localToDateCreated" />
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