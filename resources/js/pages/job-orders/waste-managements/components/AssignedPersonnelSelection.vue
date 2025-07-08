<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { ScrollArea } from '@/components/ui/scroll-area'
import { getInitials } from '@/composables/useInitials'
import { Employee, Form3Hauling } from '@/types'
import { Check, ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'

type Roles = 'teamLeader' | 'teamDriver' | 'safetyOfficer' | 'teamMechanic'

interface AssignedPersonnelSelectionProps {
  employees: Employee[]
  hauling?: Form3Hauling
  role?: Roles
  index?: number
  label: string
  open?: boolean
  areHaulers?: boolean
  selectedHaulers?: Employee[]
}

const props = withDefaults(defineProps<AssignedPersonnelSelectionProps>(), {
  areHaulers: false,
})

const isExistingHauler = (employeeId: number) => {
  return props?.selectedHaulers?.map((h) => h.id).includes(employeeId)
}

const isSingleSelect = computed(() => props.role)
</script>

<template>
  <div class="flex items-center gap-x-10">
    <Label class="w-28 shrink-0">
      {{ label }}
    </Label>
    <Popover
      v-bind="
        isSingleSelect
          ? {
              open,
              'onUpdate:open': (value) => $emit('toggled', role, index, value),
            }
          : {}
      "
    >
      <PopoverTrigger
        class="w-[400px]"
        as-child
      >
        <Button
          variant="outline"
          class="bg-muted"
        >
          <template v-if="areHaulers">
            <template v-if="selectedHaulers?.length">
              <div
                :key="selectedHaulers[0].id"
                class="flex items-center justify-between gap-2 rounded-md text-xs"
              >
                <div class="flex items-center gap-2 overflow-hidden">
                  <Avatar class="h-7 w-7 shrink-0 rounded-full">
                    <AvatarImage
                      v-if="selectedHaulers[0]?.account?.avatar"
                      :src="selectedHaulers[0].account.avatar"
                      :alt="selectedHaulers[0].fullName"
                    />
                    <AvatarFallback>
                      {{ getInitials(selectedHaulers[0].fullName) }}
                    </AvatarFallback>
                  </Avatar>
                  <span class="truncate">
                    <template v-if="selectedHaulers.length < 2">
                      {{ selectedHaulers[0].fullName }}
                    </template>
                    <template v-else>
                      {{
                        `${selectedHaulers[0].fullName} and ${selectedHaulers.length - 1} more`
                      }}
                    </template>
                  </span>
                </div>
                <Button
                  variant="ghost"
                  size="icon"
                  type="button"
                  class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                  @click="$emit('onRemoveExistingHaulers')"
                >
                  <X />
                </Button>
              </div>
            </template>
            <template v-else>
              <span class="text-muted-foreground"> Select haulers </span>
            </template>
          </template>
          <template v-else>
            <template v-if="role && hauling?.assignedPersonnel[role]">
              <Avatar class="h-7 w-7 shrink-0 rounded-full">
                <AvatarImage
                  v-if="hauling.assignedPersonnel[role]?.account?.avatar"
                  :src="hauling.assignedPersonnel[role]?.account?.avatar"
                  :alt="hauling.assignedPersonnel[role].fullName"
                />
                <AvatarFallback>
                  {{ getInitials(hauling.assignedPersonnel[role].fullName) }}
                </AvatarFallback>
              </Avatar>
              <div
                class="flex items-center justify-between gap-2 rounded-md text-xs"
              >
                {{ hauling.assignedPersonnel[role]?.fullName }}
                <Button
                  variant="ghost"
                  size="icon"
                  type="button"
                  class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                  @click="$emit('removed', role, index)"
                >
                  <X />
                </Button>
              </div>
            </template>
            <template v-else>
              <span class="text-muted-foreground">
                {{ `Select a ${label}` }}
              </span>
            </template>
          </template>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-full p-0">
        <Command>
          <CommandInput :placeholder="`Search for a ${label.toLowerCase()}`" />
          <CommandList>
            <CommandEmpty> No results found. </CommandEmpty>
            <ScrollArea class="h-72">
              <CommandGroup>
                <CommandItem
                  v-for="employee in employees"
                  :key="employee.id"
                  :value="employee"
                  @select="
                    areHaulers
                      ? $emit('onHaulerSelect', employee, index)
                      : $emit('clicked', role, employee, index)
                  "
                >
                  <div
                    v-if="areHaulers"
                    :class="[
                      'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                      isExistingHauler(employee.id)
                        ? 'bg-primary text-primary-foreground'
                        : 'opacity-50 [&_svg]:invisible',
                    ]"
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
                  <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate">
                      {{ employee.fullName }}
                    </span>
                  </div>
                  <Check
                    v-if="
                      !areHaulers && role && hauling?.assignedPersonnel[role]
                    "
                    :class="[
                      'ml-auto h-4 w-4',
                      hauling.assignedPersonnel[role]?.id === employee.id
                        ? 'opacity-100'
                        : 'opacity-0',
                    ]"
                  />
                </CommandItem>
              </CommandGroup>
            </ScrollArea>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
