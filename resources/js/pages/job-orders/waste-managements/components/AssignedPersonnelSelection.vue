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
import { getInitials } from '@/composables/useInitials'
import { Employee, Form3Hauling } from '@/types'
import { Check, ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'
import EmployeeCommandListPlaceholder from './placeholders/EmployeeCommandListPlaceholder.vue'

interface AssignedPersonnelSelectionProps {
  employees?: Employee[]
  hauling: Form3Hauling
  role: RoleType
  index: number
  label: string
  open: boolean
  canEdit: boolean
}

const props = withDefaults(defineProps<AssignedPersonnelSelectionProps>(), {
  canEdit: false,
})

const firstPersonnel = computed(
  () => props.hauling?.assignedPersonnel?.[props.role],
)

type RoleType = (typeof Roles)[number]['id']
const Roles = [
  {
    id: 'teamLeader',
    label: 'Team Leader',
  },
  {
    id: 'teamMechanic',
    label: 'Mechanic',
  },
  {
    id: 'safetyOfficer',
    label: 'Safety Officer',
  },
  {
    id: 'teamDriver',
    label: 'Driver',
  },
] as const
</script>

<template>
  <div class="flex items-center gap-x-10">
    <Label class="w-28 shrink-0">
      {{ label }}
    </Label>
    <Popover
      :open="open"
      @update:open="(value) => $emit('toggled', role, index, value)"
    >
      <PopoverTrigger
        class="w-[400px]"
        as-child
        :disabled="!canEdit"
      >
        <Button variant="outline">
          <template v-if="hauling?.assignedPersonnel?.[role]">
            <Avatar class="h-7 w-7 shrink-0 rounded-full">
              <AvatarImage
                v-if="firstPersonnel?.account?.avatar"
                :src="firstPersonnel.account.avatar"
                :alt="firstPersonnel.fullName"
              />
              <AvatarFallback>
                {{ getInitials(hauling?.assignedPersonnel[role]?.fullName) }}
              </AvatarFallback>
            </Avatar>
            <div
              class="flex items-center justify-between gap-2 rounded-md text-xs"
            >
              {{ hauling?.assignedPersonnel[role]?.fullName }}
              <Button
                v-if="canEdit"
                variant="ghost"
                size="icon"
                type="button"
                class="ml-1 h-5 w-5 text-muted-foreground hover:text-primary-foreground"
                @click="$emit('removed', role, index)"
              >
                <X />
              </Button>
            </div>
          </template>
          <template v-else>
            <span class="font-normal text-muted-foreground">
              {{ `Select a ${label}` }}
            </span>
          </template>
          <ChevronsUpDown class="ml-auto h-4 w-4" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-72 p-0" align="start">
        <Command>
          <CommandInput :placeholder="`Search for a ${label.toLowerCase()}`" />
          <CommandList>
            <CommandEmpty> No results found. </CommandEmpty>
            <CommandGroup>
              <template v-if="!employees">
                <EmployeeCommandListPlaceholder :count="7" />
              </template>
              <template v-else>
                <CommandItem
                  v-for="employee in employees"
                  :key="employee.id"
                  :value="employee"
                  class="cursor-pointer"
                  @select="$emit('clicked', role, employee, index)"
                >
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
                  <Check
                    v-if="hauling?.assignedPersonnel?.[role]"
                    :class="[
                      'ml-auto h-4 w-4',
                      hauling.assignedPersonnel[role]?.id === employee.id
                        ? 'opacity-100'
                        : 'opacity-0',
                    ]"
                  />
                </CommandItem>
              </template>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>
