<script setup lang="ts">
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { formatToDateString } from '@/composables/useDateFormatter'
import { Employee, Form3Hauling } from '@/types'
import { ref } from 'vue'
import AssignedPersonnelSelection from './AssignedPersonnelSelection.vue'

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
    haulings.value[index].assignedPersonnel[role] = null as any
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
  <div class="grid grid-cols-[auto,1fr] gap-y-6">
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
        <AccordionTrigger class="rounded-sm bg-muted px-4 hover:bg-muted">
          <div class="text-sm">
            {{ formatToDateString(hauling.date) }}
          </div>
        </AccordionTrigger>
        <AccordionContent
          class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6 px-4 py-8"
        >
          <div class="col-span-2 grid grid-cols-2 gap-x-24">
            <AssignedPersonnelSelection
              :employees="employees"
              :hauling="hauling"
              :role="'teamLeader'"
              :index="index"
              :label="'Team Leader'"
              :open="isPopoverOpen.teamLeader === index"
              @toggled="onPopoverToggle"
              @clicked="handleAssignedPersonnelChanges"
              @removed="removeAssignedPersonnel"
            />
            <AssignedPersonnelSelection
              :employees="employees"
              :hauling="hauling"
              role="safetyOfficer"
              :index="index"
              label="Safety Officer"
              :open="isPopoverOpen.safetyOfficer === index"
              @toggled="onPopoverToggle"
              @clicked="handleAssignedPersonnelChanges"
              @removed="removeAssignedPersonnel"
            />
          </div>

          <div class="col-span-2 grid grid-cols-2 gap-x-24">
            <AssignedPersonnelSelection
              :employees="employees"
              :hauling="hauling"
              role="teamDriver"
              :index="index"
              label="Driver"
              :open="isPopoverOpen.teamDriver === index"
              @toggled="onPopoverToggle"
              @clicked="handleAssignedPersonnelChanges"
              @removed="removeAssignedPersonnel"
            />
            <AssignedPersonnelSelection
              :employees="employees"
              :hauling="hauling"
              role="teamMechanic"
              :index="index"
              label="Mechanic"
              :open="isPopoverOpen.teamMechanic === index"
              @toggled="onPopoverToggle"
              @clicked="handleAssignedPersonnelChanges"
              @removed="removeAssignedPersonnel"
            />
          </div>

          <div class="col-span-2 grid grid-cols-2 gap-x-24">
            <AssignedPersonnelSelection
              :employees="employees"
              :are-haulers="true"
              label="Haulers"
              :selectedHaulers="hauling.haulers"
              :open="true"
              :index="index"
              @on-hauler-select="handleHaulerMultiSelection"
              @on-remove-existing-haulers="removeExistingHaulers"
            />
            <div class="flex items-center gap-x-10">
              <Label
                :for="'truckNo-' + hauling.id"
                class="w-28 shrink-0"
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
  </div>
</template>
