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
import { Employee, Form3Hauling } from '@/types'
import { ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'
import EmployeeCommandListPlaceholder from './placeholders/EmployeeCommandListPlaceholder.vue'

interface HaulersSelectionProps {
  employees?: Employee[]
  hauling: Form3Hauling
  index: number
  isAuthorize: boolean
}

interface HaulersSelectionEmits {
  onHaulerToggle: void
  onRemoveExistingHaulers: [index: number]
  onHaulerSelect: [hauler: Employee, index: number]
}

const props = withDefaults(defineProps<HaulersSelectionProps>(), {
  isAuthorize: false,
})

defineEmits<HaulersSelectionEmits>()

const haulers = computed(() => props.hauling.haulers)

const remainingEmployees = computed(() => {
  const filtered = props.employees?.filter(
    (employee) =>
      !haulers.value.map((hauler) => hauler.id).includes(employee.id),
  )
  return new Set(filtered)
})

const firstHauler = computed(() => haulers.value?.[0])

const canEdit = computed(() => props.isAuthorize && props.hauling.isOpen)
</script>

<template>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-x-10">
    <Label class="w-full shrink-0 sm:w-28">Haulers</Label>
    <Popover @update:open="$emit('onHaulerToggle')">
      <PopoverTrigger
        class="w-full sm:w-[400px]"
        as-child
        :disabled="
          (!hauling.isOpen || hauling.status === 'in progress') &&
          haulers.length < 2
        "
      >
        <Button
          variant="outline"
          class="w-full"
        >
          <div
            v-if="haulers?.length"
            class="flex items-center justify-between gap-2 rounded-md text-xs"
          >
            <div class="flex items-center gap-2 overflow-hidden">
              <UserAvatar
                :avatar-path="firstHauler?.account?.avatar"
                :fallback="firstHauler.fullName"
              />
              <span class="truncate">
                <template v-if="haulers.length < 2">
                  {{ firstHauler.fullName }}
                </template>
                <template v-else>
                  {{ `${firstHauler.fullName} and ${haulers.length - 1} more` }}
                </template>
              </span>
            </div>
            <Button
              v-if="canEdit"
              variant="ghost"
              size="icon"
              type="button"
              class="ml-1 h-5 w-5 text-muted-foreground hover:text-primary-foreground"
              @click="$emit('onRemoveExistingHaulers', index)"
            >
              <X />
            </Button>
          </div>
          <template v-else>
            <span class="font-normal text-muted-foreground">
              Select haulers
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
          <CommandInput placeholder="Search for haulers" />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <template v-if="haulers?.length">
              <div :class="['overflow-y-auto', { 'max-h-40': hauling.isOpen }]">
                <CommandGroup>
                  <CommandItem
                    v-for="hauler in haulers"
                    :key="hauler.id"
                    :value="hauler"
                    class="cursor-pointer"
                    @select="$emit('onHaulerSelect', hauler, index)"
                  >
                    <EmployeePopoverSelection
                      :employee="hauler"
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
                  class="cursor-pointer"
                  @select="$emit('onHaulerSelect', employee, index)"
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
