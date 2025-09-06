<script setup lang="ts">
import EmployeePopoverSelection from '@/components/EmployeePopoverSelection.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
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
import { getInitials } from '@/composables/useInitials'
import { Employee, Form3Hauling } from '@/types'
import { Check, ChevronsUpDown, X } from 'lucide-vue-next'
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

const isExistingHauler = (employeeId: number) => {
  return haulers.value.map((hauler) => hauler.id).includes(employeeId)
}

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
  <div class="flex items-center gap-x-10">
    <Label class="w-28 shrink-0"> Haulers </Label>
    <Popover @update:open="$emit('onHaulerToggle')">
      <PopoverTrigger
        class="w-[400px]"
        as-child
        :disabled="
          (!hauling.isOpen || hauling.status === 'in progress') &&
          haulers.length < 2
        "
      >
        <Button variant="outline">
          <div
            v-if="haulers?.length"
            class="flex items-center justify-between gap-2 rounded-md text-xs"
          >
            <div class="flex items-center gap-2 overflow-hidden">
              <Avatar class="h-7 w-7 shrink-0 rounded-full">
                <AvatarImage
                  v-if="firstHauler?.account?.avatar"
                  :src="firstHauler.account.avatar"
                  :alt="firstHauler.fullName"
                />
                <AvatarFallback>
                  {{ getInitials(firstHauler.fullName) }}
                </AvatarFallback>
              </Avatar>
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
            <CommandEmpty> No results found. </CommandEmpty>
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
                  <div
                    v-if="!isExistingHauler(employee.id)"
                    class="mr-1 flex h-4 w-4 items-center justify-center rounded-sm border border-primary opacity-50 [&_svg]:invisible"
                  >
                    <Check :class="['h-4 w-4']" />
                  </div>
                  <Avatar class="h-7 w-7 overflow-hidden rounded-full">
                    <AvatarImage
                      v-if="employee?.account?.avatar"
                      :src="employee.account.avatar"
                      :alt="employee.fullName"
                    />
                    <AvatarFallback class="rounded-full">
                      {{ getInitials(employee.fullName) }}
                    </AvatarFallback>
                  </Avatar>
                  <div class="grid flex-1 text-left text-[13px] leading-tight">
                    <span class="truncate">
                      {{ employee.fullName }}
                    </span>
                  </div>
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
