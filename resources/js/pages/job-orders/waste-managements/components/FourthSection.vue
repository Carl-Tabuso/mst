<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
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
import { cn } from '@/lib/utils'
import { Employee } from '@/types'
import { Check, CheckIcon, ChevronsUpDown } from 'lucide-vue-next'
import { ref } from 'vue'

interface FourthSectionProps {
  employees: Employee[]
}

defineProps<FourthSectionProps>()

const teamLeader = defineModel<Employee>('teamLeader')
const safetyOfficer = defineModel<Employee>('safetyOfficer')
const teamDriver = defineModel<Employee>('teamDriver')
const teamMechanic = defineModel<Employee>('teamMechanic')
const haulers = defineModel<Employee[]>('haulers')
const truckNumber = defineModel<string>('truckNumber')

const isExistingHauler = (employeeId: number) => {
  return haulers.value?.map((h) => h.id).includes(employeeId)
}

const handleHaulerMultiSelection = (employee: Employee) => {
  if (haulers.value === undefined) {
    console.log('hello')
    haulers.value = []
  }

  if (isExistingHauler(employee.id)) {
    const index = haulers.value.findIndex((h) => h.id === employee.id)
    haulers.value.splice(index, 1)
  } else {
    haulers.value.push(employee)
  }
}

const handleTeamLeaderChange = (employee: Employee) => {
  teamLeader.value = employee
  isTeamLeaderPopoverOpen.value = false
}

const handleSafetyOfficerChange = (employee: Employee) => {
  safetyOfficer.value = employee
  isSafetyOfficerPopoverOpen.value = false
}

const handleTeamDriverChange = (employee: Employee) => {
  teamDriver.value = employee
  isDriverPopoverOpen.value = false
}

const handleMechanicChange = (employee: Employee) => {
  teamMechanic.value = employee
  isMechnicPopoverOpen.value = false
}

const isTeamLeaderPopoverOpen = ref<boolean>(false)
const isSafetyOfficerPopoverOpen = ref<boolean>(false)
const isDriverPopoverOpen = ref<boolean>(false)
const isMechnicPopoverOpen = ref<boolean>(false)
</script>

<template>
  <div class="col-span-2 grid grid-cols-2 gap-x-24">
    <div class="flex items-center gap-x-4">
      <Label
        for="teamLeader"
        class="w-48 shrink-0"
      >
        Team Leader
      </Label>
      <Popover v-model:open="isTeamLeaderPopoverOpen">
        <PopoverTrigger
          class="w-[400px]"
          as-child
        >
          <Button
            variant="outline"
            class=""
          >
            <template v-if="teamLeader">
              <Badge
                class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                variant="secondary"
                >{{ teamLeader?.fullName }}
              </Badge>
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
                  @select="() => handleTeamLeaderChange(employee)"
                >
                  <span>
                    {{ employee.fullName }}
                  </span>
                  <Check
                    :class="
                      cn(
                        'ml-auto h-4 w-4',
                        teamLeader?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      )
                    "
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
        for="safetyOfficer"
        class="w-36 shrink-0"
      >
        Safety Officer
      </Label>
      <Popover v-model:open="isSafetyOfficerPopoverOpen">
        <PopoverTrigger
          class="w-[400px]"
          as-child
        >
          <Button
            variant="outline"
            class=""
          >
            <template v-if="safetyOfficer">
              <Badge
                class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                variant="secondary"
                >{{ safetyOfficer?.fullName }}
              </Badge>
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
                  @select="() => handleSafetyOfficerChange(employee)"
                >
                  <span>
                    {{ employee.fullName }}
                  </span>
                  <Check
                    :class="
                      cn(
                        'ml-auto h-4 w-4',
                        safetyOfficer?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      )
                    "
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
        for="driver"
        class="w-48 shrink-0"
      >
        Driver
      </Label>
      <Popover v-model:open="isDriverPopoverOpen">
        <PopoverTrigger
          class="w-[400px]"
          as-child
        >
          <Button
            variant="outline"
            class=""
          >
            <template v-if="teamDriver">
              <Badge
                class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                variant="secondary"
                >{{ teamDriver?.fullName }}
              </Badge>
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
                  @select="() => handleTeamDriverChange(employee)"
                >
                  <span>
                    {{ employee.fullName }}
                  </span>
                  <Check
                    :class="
                      cn(
                        'ml-auto h-4 w-4',
                        teamDriver?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      )
                    "
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
        for="teamMechanic"
        class="w-36 shrink-0"
      >
        Mechanic
      </Label>
      <Popover v-model:open="isMechnicPopoverOpen">
        <PopoverTrigger
          class="w-[400px]"
          as-child
        >
          <Button
            variant="outline"
            class="justify-start"
          >
            <template v-if="teamMechanic">
              <Badge
                class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                variant="secondary"
                >{{ teamMechanic?.fullName }}
              </Badge>
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
                  @select="() => handleMechanicChange(employee)"
                >
                  <span>
                    {{ employee.fullName }}
                  </span>
                  <Check
                    :class="
                      cn(
                        'ml-auto h-4 w-4',
                        teamMechanic?.id === employee.id
                          ? 'opacity-100'
                          : 'opacity-0',
                      )
                    "
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
        for="haulers"
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
            class="justify-start"
          >
            <template v-if="haulers?.length">
              <template v-if="haulers?.length < 3">
                <Badge
                  v-for="hauler in haulers?.slice(0, 2)"
                  :key="hauler.id"
                  class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                  variant="secondary"
                  >{{ hauler.fullName }}
                </Badge>
              </template>
              <template v-else>
                <Badge
                  v-for="hauler in haulers?.slice(0, 1)"
                  :key="hauler.id"
                  class="rounded-full font-normal"
                  variant="secondary"
                  >{{ `${hauler.fullName} and ${haulers?.length - 1} more` }}
                </Badge>
              </template>
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
                  @select="() => handleHaulerMultiSelection(employee)"
                >
                  <div
                    :class="
                      cn(
                        'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                        isExistingHauler(employee.id)
                          ? 'bg-primary text-primary-foreground'
                          : 'opacity-50 [&_svg]:invisible',
                      )
                    "
                  >
                    <CheckIcon :class="cn('h-4 w-4')" />
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
        Truck #
      </Label>
      <Input
        id="truckNo"
        placeholder="Enter truck no."
        v-model="truckNumber"
        class="w-full"
      />
    </div>
  </div>
</template>
