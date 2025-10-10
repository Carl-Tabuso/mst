<script setup lang="ts">
import EmployeePopoverSelection from '@/components/EmployeePopoverSelection.vue'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import UserAvatar from '@/components/UserAvatar.vue'
import { Employee } from '@/types'
import { ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'
import EmployeeCommandListPlaceholder from './placeholders/EmployeeCommandListPlaceholder.vue'

interface DriversSelectionProps {
  employees?: Employee[]
  isAuthorize: boolean
  isOpen: boolean
}

interface DriversSelectionEmits {
  (e: 'onDriverToggle'): void
}

const props = defineProps<DriversSelectionProps>()

const emit = defineEmits<DriversSelectionEmits>()

const selectedDrivers = defineModel<Employee[]>('drivers', { default: [] })

const remainingEmployees = computed(() => {
  const filtered = props.employees?.filter((employee) => {
    const selectedDriverIds = selectedDrivers.value.map((driver) => driver.id)
    return !selectedDriverIds.includes(employee.id)
  })
  return new Set(filtered)
})

const onDriverSelect = (driver: Employee) => {
  if (!props.isAuthorize || !props.isOpen) return

  const isSelected = selectedDrivers.value.includes(driver)
  if (isSelected) {
    const index = selectedDrivers.value.findIndex(
      (sdriver) => sdriver.id === driver.id,
    )
    selectedDrivers.value.splice(index, 1)
  } else {
    selectedDrivers.value.push(driver)
  }
}

const firstDriver = computed(() => selectedDrivers.value?.[0])

const canEdit = computed(() => props.isAuthorize && props.isOpen)
</script>

<template>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-x-10">
    <Label class="w-full shrink-0 sm:w-28">Drivers</Label>
    <Popover @update:open="emit('onDriverToggle')">
      <PopoverTrigger
        class="w-full sm:w-[400px]"
        as-child
      >
        <Button
          variant="outline"
          class="w-full"
        >
          <div
            v-if="selectedDrivers?.length"
            class="flex items-center justify-between gap-2 rounded-md text-xs"
          >
            <div class="flex flex-row items-center gap-2 overflow-hidden">
              <UserAvatar
                :avatar-path="firstDriver?.account?.avatar"
                :fallback="firstDriver.fullName"
              />
              <span class="truncate">
                <template v-if="selectedDrivers.length < 2">
                  {{ firstDriver.fullName }}
                </template>
                <template v-else>
                  {{
                    `${firstDriver.fullName} and ${selectedDrivers.length - 1} more`
                  }}
                </template>
              </span>
            </div>
            <Button
              v-if="canEdit"
              variant="ghost"
              size="icon"
              type="button"
              class="ml-1 h-5 w-5 text-muted-foreground"
              @click="selectedDrivers = []"
            >
              <X />
            </Button>
          </div>
          <template v-else>
            <span class="font-normal text-muted-foreground">
              Select drivers
            </span>
          </template>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent
        class="w-72 p-0"
        align="start"
      >
        <Command>
          <CommandInput placeholder="Search for drivers" />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <template v-if="selectedDrivers.length">
              <div :class="['overflow-y-auto', { 'max-h-40': canEdit }]">
                <CommandGroup>
                  <CommandItem
                    v-for="driver in selectedDrivers"
                    :key="driver.id"
                    :value="driver"
                    @select="onDriverSelect(driver)"
                  >
                    <EmployeePopoverSelection
                      :employee="driver"
                      is-selected
                    />
                  </CommandItem>
                </CommandGroup>
              </div>
              <CommandSeparator v-if="canEdit" />
            </template>
            <CommandGroup v-if="canEdit">
              <template v-if="!employees">
                <EmployeeCommandListPlaceholder :count="7" />
              </template>
              <template v-else>
                <CommandItem
                  v-for="employee in remainingEmployees"
                  :key="employee.id"
                  :value="employee"
                  @select="onDriverSelect(employee)"
                >
                  <EmployeePopoverSelection :employee="employee" />
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
