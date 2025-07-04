<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
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
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { formatToDateString } from '@/composables/useDateFormatter'
import { Employee, Form3Hauling } from '@/types'
import { Check, CheckIcon, ChevronsUpDown, X } from 'lucide-vue-next'
import { ref } from 'vue'
import { getInitials } from '@/composables/useInitials'

interface FourthSectionProps {
  employees: Employee[]
}

defineProps<FourthSectionProps>()

const haulings = defineModel<Form3Hauling[]>('haulings')

const isExistingHauler = (employeeId: number, index: number) => {
  if (haulings.value) {
    return haulings.value[index].haulers.map((h) => h.id).includes(employeeId)
  }
}

const handleHaulerMultiSelection = (employee: Employee, index: number) => {
  if (haulings.value) {
    if (isExistingHauler(employee.id, index)) {
      const objIndex = haulings.value[index].haulers.findIndex((h) => h.id === employee.id)
      haulings.value[index].haulers.splice(objIndex, 1)
    } else {
      haulings.value[index].haulers.push(employee)
    }
  }
}

const handleTeamLeaderChange = (employee: Employee, index: number) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel.teamLeader = employee
    isTeamLeaderPopoverOpen.value = null
  }
}

const handleSafetyOfficerChange = (employee: Employee, index: number) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel.safetyOfficer = employee
    isSafetyOfficerPopoverOpen.value = null
  }
}

const handleTeamDriverChange = (employee: Employee, index: number) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel.teamDriver = employee
    isDriverPopoverOpen.value = null    
  }
}

const handleMechanicChange = (employee: Employee, index: number) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel.teamMechanic = employee
    isMechnicPopoverOpen.value = null    
  }
}

const isTeamLeaderPopoverOpen = ref<number | null>(null)
const isSafetyOfficerPopoverOpen = ref<number | null>(null)
const isDriverPopoverOpen = ref<number | null>(null)
const isMechnicPopoverOpen = ref<number | null>(null)

const onTeamLeaderPopoverToggle = (index: number, value: boolean) => {
  isTeamLeaderPopoverOpen.value = value ? index : null;
};

const onSafetyOfficerPopoverToggle = (index: number, value: boolean) => {
  isSafetyOfficerPopoverOpen.value = value ? index : null;
};

const onDriverPopoverToggle = (index: number, value: boolean) => {
  isDriverPopoverOpen.value = value ? index : null;
};

const onMechanicPopoverToggle = (index: number, value: boolean) => {
  isMechnicPopoverOpen.value = value ? index : null;
};
</script>

<template>
  <div class="text-1xl font-bold">
    Assigned Personnel
  </div>
  <template v-for="(hauling, index) in haulings" :key="hauling.id">
    <div>
      {{ formatToDateString(hauling.date) }}
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          :for="'teamLeader-' + hauling.id"
          class="w-48 shrink-0"
        >
          Team Leader
        </Label>
        <Popover
          :open="isTeamLeaderPopoverOpen === index"
          @update:open="(value: boolean) => onTeamLeaderPopoverToggle(index, value)"
        >
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class="bg-muted"
            >
              <template v-if="hauling.assignedPersonnel.teamLeader">
                <Avatar class="h-6 w-6 rounded-full shrink-0">
                  <AvatarImage
                    v-if="hauling.assignedPersonnel.teamLeader?.account?.avatar"
                    :src="hauling.assignedPersonnel.teamLeader.account.avatar"
                    :alt="hauling.assignedPersonnel.teamLeader.fullName"
                  />
                  <AvatarFallback>
                    {{ getInitials(hauling.assignedPersonnel.teamLeader.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex items-center justify-between gap-2 rounded-md text-xs">
                  {{ hauling.assignedPersonnel.teamLeader?.fullName }}
                </div>
              </template>
              <template v-else>
                <span class="text-muted-foreground"> Select a Team Leader </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for a team leader" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in employees"
                    :key="employee.id"
                    :value="employee"
                    @select="() => handleTeamLeaderChange(employee, index)"
                  >
                  <Avatar class="h-6 w-6 overflow-hidden rounded-full">
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
                      :class="[
                        'ml-auto h-4 w-4',
                        hauling.assignedPersonnel.teamLeader?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
      <div class="flex items-center">
        <Label
          :for="'safetyOfficer-' + hauling.id"
          class="w-36 shrink-0"
        >
          Safety Officer
        </Label>
        <Popover
          :open="isSafetyOfficerPopoverOpen === index"
          @update:open="(value: boolean) => onSafetyOfficerPopoverToggle(index, value)"
        >
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class="bg-muted"
            >
              <template v-if="hauling.assignedPersonnel.safetyOfficer">
                <Avatar class="h-6 w-6 rounded-full shrink-0">
                  <AvatarImage
                    v-if="hauling.assignedPersonnel.safetyOfficer?.account?.avatar"
                    :src="hauling.assignedPersonnel.safetyOfficer.account.avatar"
                    :alt="hauling.assignedPersonnel.safetyOfficer.fullName"
                  />
                  <AvatarFallback>
                    {{ getInitials(hauling.assignedPersonnel.safetyOfficer.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex items-center justify-between gap-2 rounded-md text-xs">
                  {{ hauling.assignedPersonnel.safetyOfficer?.fullName }}
                </div>
              </template>
              <template v-else>
                <span class="text-muted-foreground">
                  Select a Safety Officer
                </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for a safety officer" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in employees"
                    :key="employee.id"
                    :value="employee"
                    @select="() => handleSafetyOfficerChange(employee, index)"
                  >
                    <span>
                      {{ employee.fullName }}
                    </span>
                    <Check
                      :class="[
                        'ml-auto h-4 w-4',
                        hauling.assignedPersonnel.safetyOfficer?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
    </div>

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          :for="'driver-' + hauling.id"
          class="w-48 shrink-0"
        >
          Driver
        </Label>
        <Popover
          :open="isDriverPopoverOpen === index"
          @update:open="(value: boolean) => onDriverPopoverToggle(index, value)"
        >
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class="bg-muted"
            >
              <template v-if="hauling.assignedPersonnel.teamDriver">
                <Avatar class="h-6 w-6 rounded-full shrink-0">
                  <AvatarImage
                    v-if="hauling.assignedPersonnel.teamDriver?.account?.avatar"
                    :src="hauling.assignedPersonnel.teamDriver.account.avatar"
                    :alt="hauling.assignedPersonnel.teamDriver.fullName"
                  />
                  <AvatarFallback>
                    {{ getInitials(hauling.assignedPersonnel.teamDriver.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex items-center justify-between gap-2 rounded-md text-xs">
                  {{ hauling.assignedPersonnel.teamDriver?.fullName }}
                </div>
              </template>
              <template v-else>
                <span class="text-muted-foreground"> Select a Driver </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for a driver" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in employees"
                    :key="employee.id"
                    :value="employee"
                    @select="() => handleTeamDriverChange(employee, index)"
                  >
                    <span>
                      {{ employee.fullName }}
                    </span>
                    <Check
                      :class="[
                        'ml-auto h-4 w-4',
                        hauling.assignedPersonnel.teamDriver?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
      <div class="flex items-center">
        <Label
          :for="'teamMechanic-' + hauling.id"
          class="w-36 shrink-0"
        >
          Mechanic
        </Label>
        <Popover 
          :open="isMechnicPopoverOpen === index"
          @update:open="(value: boolean) => onMechanicPopoverToggle(index, value)"
        >
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class="bg-muted"
            >
              <template v-if="hauling.assignedPersonnel.teamMechanic">
                <Avatar class="h-6 w-6 rounded-full shrink-0">
                  <AvatarImage
                    v-if="hauling.assignedPersonnel.teamMechanic?.account?.avatar"
                    :src="hauling.assignedPersonnel.teamMechanic.account.avatar"
                    :alt="hauling.assignedPersonnel.teamMechanic.fullName"
                  />
                  <AvatarFallback>
                    {{ getInitials(hauling.assignedPersonnel.teamMechanic.fullName) }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex items-center justify-between gap-2 rounded-md text-xs">
                  {{ hauling.assignedPersonnel.teamMechanic?.fullName }}
                </div>
              </template>
              <template v-else>
                <span class="text-muted-foreground"> Select a Mechanic </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for a Mechanic" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in employees"
                    :key="employee.id"
                    :value="employee"
                    @select="() => handleMechanicChange(employee, index)"
                  >
                    <span>
                      {{ employee.fullName }}
                    </span>
                    <Check
                      :class="[
                        'ml-auto h-4 w-4',
                        hauling.assignedPersonnel.teamMechanic?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      ]"
                    />
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
    </div>

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          :for="'haulers-' + hauling.id"
          class="w-48 shrink-0"
        >
          Haulers
        </Label>
        <Popover>
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class="bg-muted"
            >
              <template v-if="hauling.haulers?.length">
                <div
                  :key="hauling.haulers[0].id"
                  class="flex items-center justify-between gap-2 rounded-md text-xs"
                >
                  <div class="flex items-center gap-2 overflow-hidden">
                    <Avatar class="h-6 w-6 rounded-full shrink-0">
                      <AvatarImage
                        v-if="hauling.haulers[0]?.account?.avatar"
                        :src="hauling.haulers[0].account.avatar"
                        :alt="hauling.haulers[0].fullName"
                      />
                      <AvatarFallback>
                        {{ getInitials(hauling.haulers[0].fullName) }}
                      </AvatarFallback>
                    </Avatar>
                    <span class="truncate">
                      <template v-if="hauling.haulers.length < 2">
                        {{ hauling.haulers[0].fullName }}
                      </template>
                      <template v-else>
                        {{ `${hauling.haulers[0].fullName} and ${hauling.haulers.length - 1} more` }}
                      </template>
                    </span>
                  </div>
                  <Button
                    v-if="hauling.haulers.length < 2"
                    variant="ghost"
                    size="icon"
                    type="button"
                    class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                    @click="() => handleHaulerMultiSelection(hauling.haulers[0])"
                  >
                    <X />
                  </Button>
                </div>
              </template>
              <template v-else>
                <span class="text-muted-foreground"> Select haulers </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for haulers" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <CommandGroup>
                  <CommandItem
                    v-for="employee in employees"
                    :key="employee.id"
                    :value="employee"
                    @select="() => handleHaulerMultiSelection(employee, index)"
                  >
                    <div
                      :class="[
                        'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                        isExistingHauler(employee.id, index)
                          ? 'bg-primary text-primary-foreground'
                          : 'opacity-50 [&_svg]:invisible',                        
                      ]"
                    >
                      <CheckIcon :class="['h-4 w-4']" />
                    </div>
                    <span>
                      {{ employee.fullName }}
                    </span>
                  </CommandItem>
                </CommandGroup>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
      <div class="flex items-center">
        <Label
          for="truckNo"
          class="w-36 shrink-0"
        >
          Truck Number
        </Label>
        <Input
          id="truckNo"
          placeholder="Enter truck plate number"
          v-model="hauling.truckNo"
          class="w-full"
        />
      </div>
    </div>

    <Separator v-if="index !== haulings.length - 1" class="col-[1/-1] my-2 w-full" />
  </template>
</template>
