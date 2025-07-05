<script setup lang="ts">
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
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
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { Employee, Form3Hauling } from '@/types'
import { Check, CheckIcon, ChevronsUpDown, X } from 'lucide-vue-next'
import { ref } from 'vue'

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
      const objIndex = haulings.value[index].haulers.findIndex(
        (h) => h.id === employee.id,
      )
      haulings.value[index].haulers.splice(objIndex, 1)
    } else {
      haulings.value[index].haulers.push(employee)
    }
  }
}

const removeExistingHaulers = (index: number) => {
  if (haulings.value) {
    haulings.value[index].haulers = []
  }
}

type Roles = 'teamLeader' | 'teamMechanic' | 'safetyOfficer' | 'teamDriver'

const handleAssignedPersonnelChanges = (
  role: Roles,
  employee: Employee,
  index: number,
) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel[role] = employee
    isPopoverOpen.value[role] = null
  }
}

const removeAssignedPersonnel = (role: Roles, index: number) => {
  if (haulings.value) {
    haulings.value[index].assignedPersonnel[role] = null
  }
}

const isPopoverOpen = ref<Record<string, number | null>>({
  teamLeader: null,
  safetyOfficer: null,
  teamDriver: null,
  teamMechanic: null,
})

const onPopoverToggle = (role: Roles, index: number, value: boolean) => {
  isPopoverOpen.value[role] = value ? index : null
}
</script>

<template>
  <div v-if="haulings?.length">
    <div class="text-xl font-semibold">Assigned Personnel</div>
    <p class="text-sm text-muted-foreground">
      List of assigned personnel for each hauling operations.
    </p>
  </div>
  <Accordion
    type="multiple"
    class="col-[1/-1] w-full"
    collapsible
  >
    <AccordionItem
      v-for="(hauling, index) in haulings"
      :key="hauling.id"
      :value="String(hauling.id)"
    >
      <AccordionTrigger class="rounded-md bg-muted px-4 hover:bg-muted">
        {{ formatToDateString(hauling.date) }}
      </AccordionTrigger>
      <AccordionContent
        class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6 px-4 py-8"
      >
        <div class="col-span-2 grid grid-cols-2 gap-x-24">
          <div class="flex items-center gap-x-4">
            <Label
              :for="'teamLeader-' + hauling.id"
              class="w-48 shrink-0"
            >
              Team Leader
            </Label>
            <Popover
              :open="isPopoverOpen.teamLeader === index"
              @update:open="
                (value: boolean) => onPopoverToggle('teamLeader', index, value)
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
                  <template v-if="hauling.assignedPersonnel.teamLeader">
                    <Avatar class="h-7 w-7 shrink-0 rounded-full">
                      <AvatarImage
                        v-if="
                          hauling.assignedPersonnel.teamLeader?.account?.avatar
                        "
                        :src="
                          hauling.assignedPersonnel.teamLeader.account.avatar
                        "
                        :alt="hauling.assignedPersonnel.teamLeader.fullName"
                      />
                      <AvatarFallback>
                        {{
                          getInitials(
                            hauling.assignedPersonnel.teamLeader.fullName,
                          )
                        }}
                      </AvatarFallback>
                    </Avatar>
                    <div
                      class="flex items-center justify-between gap-2 rounded-md text-xs"
                    >
                      {{ hauling.assignedPersonnel.teamLeader?.fullName }}
                      <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                        @click="
                          () => removeAssignedPersonnel('teamLeader', index)
                        "
                      >
                        <X />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <span class="text-muted-foreground">
                      Select a Team Leader
                    </span>
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
                        @select="
                          () =>
                            handleAssignedPersonnelChanges(
                              'teamLeader',
                              employee,
                              index,
                            )
                        "
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
                        <div
                          class="grid flex-1 text-left text-sm leading-tight"
                        >
                          <span class="truncate">
                            {{ employee.fullName }}
                          </span>
                        </div>
                        <Check
                          :class="[
                            'ml-auto h-4 w-4',
                            hauling.assignedPersonnel.teamLeader?.id ===
                            employee.id
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
              :open="isPopoverOpen.safetyOfficer === index"
              @update:open="
                (value: boolean) =>
                  onPopoverToggle('safetyOfficer', index, value)
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
                  <template v-if="hauling.assignedPersonnel.safetyOfficer">
                    <Avatar class="h-7 w-7 shrink-0 rounded-full">
                      <AvatarImage
                        v-if="
                          hauling.assignedPersonnel.safetyOfficer?.account
                            ?.avatar
                        "
                        :src="
                          hauling.assignedPersonnel.safetyOfficer.account.avatar
                        "
                        :alt="hauling.assignedPersonnel.safetyOfficer.fullName"
                      />
                      <AvatarFallback>
                        {{
                          getInitials(
                            hauling.assignedPersonnel.safetyOfficer.fullName,
                          )
                        }}
                      </AvatarFallback>
                    </Avatar>
                    <div
                      class="flex items-center justify-between gap-2 rounded-md text-xs"
                    >
                      {{ hauling.assignedPersonnel.safetyOfficer?.fullName }}
                      <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                        @click="
                          () => removeAssignedPersonnel('safetyOfficer', index)
                        "
                      >
                        <X />
                      </Button>
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
                        @select="
                          () =>
                            handleAssignedPersonnelChanges(
                              'safetyOfficer',
                              employee,
                              index,
                            )
                        "
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
                        <div
                          class="grid flex-1 text-left text-sm leading-tight"
                        >
                          <span class="truncate">
                            {{ employee.fullName }}
                          </span>
                        </div>
                        <Check
                          :class="[
                            'ml-auto h-4 w-4',
                            hauling.assignedPersonnel.safetyOfficer?.id ===
                            employee.id
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
              :open="isPopoverOpen.teamDriver === index"
              @update:open="
                (value: boolean) => onPopoverToggle('teamDriver', index, value)
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
                  <template v-if="hauling.assignedPersonnel.teamDriver">
                    <Avatar class="h-7 w-7 shrink-0 rounded-full">
                      <AvatarImage
                        v-if="
                          hauling.assignedPersonnel.teamDriver?.account?.avatar
                        "
                        :src="
                          hauling.assignedPersonnel.teamDriver.account.avatar
                        "
                        :alt="hauling.assignedPersonnel.teamDriver.fullName"
                      />
                      <AvatarFallback>
                        {{
                          getInitials(
                            hauling.assignedPersonnel.teamDriver.fullName,
                          )
                        }}
                      </AvatarFallback>
                    </Avatar>
                    <div
                      class="flex items-center justify-between gap-2 rounded-md text-xs"
                    >
                      {{ hauling.assignedPersonnel.teamDriver?.fullName }}
                      <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                        @click="
                          () => removeAssignedPersonnel('teamDriver', index)
                        "
                      >
                        <X />
                      </Button>
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
                        @select="
                          () =>
                            handleAssignedPersonnelChanges(
                              'teamDriver',
                              employee,
                              index,
                            )
                        "
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
                        <div
                          class="grid flex-1 text-left text-sm leading-tight"
                        >
                          <span class="truncate">
                            {{ employee.fullName }}
                          </span>
                        </div>
                        <Check
                          :class="[
                            'ml-auto h-4 w-4',
                            hauling.assignedPersonnel.teamDriver?.id ===
                            employee.id
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
              :open="isPopoverOpen.teamMechanic === index"
              @update:open="
                (value: boolean) =>
                  onPopoverToggle('teamMechanic', index, value)
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
                  <template v-if="hauling.assignedPersonnel.teamMechanic">
                    <Avatar class="h-7 w-7 shrink-0 rounded-full">
                      <AvatarImage
                        v-if="
                          hauling.assignedPersonnel.teamMechanic?.account
                            ?.avatar
                        "
                        :src="
                          hauling.assignedPersonnel.teamMechanic.account.avatar
                        "
                        :alt="hauling.assignedPersonnel.teamMechanic.fullName"
                      />
                      <AvatarFallback>
                        {{
                          getInitials(
                            hauling.assignedPersonnel.teamMechanic.fullName,
                          )
                        }}
                      </AvatarFallback>
                    </Avatar>
                    <div
                      class="flex items-center justify-between gap-2 rounded-md text-xs"
                    >
                      {{ hauling.assignedPersonnel.teamMechanic?.fullName }}
                      <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                        @click="
                          () => removeAssignedPersonnel('teamMechanic', index)
                        "
                      >
                        <X />
                      </Button>
                    </div>
                  </template>
                  <template v-else>
                    <span class="text-muted-foreground">
                      Select a Mechanic
                    </span>
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
                        @select="
                          () =>
                            handleAssignedPersonnelChanges(
                              'teamMechanic',
                              employee,
                              index,
                            )
                        "
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
                        <div
                          class="grid flex-1 text-left text-sm leading-tight"
                        >
                          <span class="truncate">
                            {{ employee.fullName }}
                          </span>
                        </div>
                        <Check
                          :class="[
                            'ml-auto h-4 w-4',
                            hauling.assignedPersonnel.teamMechanic?.id ===
                            employee.id
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
                        <Avatar class="h-7 w-7 shrink-0 rounded-full">
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
                            {{
                              `${hauling.haulers[0].fullName} and ${hauling.haulers.length - 1} more`
                            }}
                          </template>
                        </span>
                      </div>
                      <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                        @click="() => removeExistingHaulers(index)"
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
                        @select="
                          () => handleHaulerMultiSelection(employee, index)
                        "
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
                        <div
                          class="grid flex-1 text-left text-sm leading-tight"
                        >
                          <span class="truncate">
                            {{ employee.fullName }}
                          </span>
                        </div>
                      </CommandItem>
                    </CommandGroup>
                  </CommandList>
                </Command>
              </PopoverContent>
            </Popover>
          </div>
          <div class="flex items-center">
            <Label
              :for="'truckNo-' + hauling.id"
              class="w-36 shrink-0"
            >
              Truck Number
            </Label>
            <Input
              :id="'truckNo-' + hauling.id"
              placeholder="Enter truck plate number"
              v-model="hauling.truckNo"
              class="w-full"
            />
          </div>
        </div>
      </AccordionContent>
    </AccordionItem>
  </Accordion>
</template>
