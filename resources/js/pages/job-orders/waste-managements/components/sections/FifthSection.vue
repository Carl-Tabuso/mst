<script setup lang="ts">
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { Employee, Form3Hauling } from '@/types'
import { ClipboardCheck, FilePenLine } from 'lucide-vue-next'
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import AssignedPersonnelSelection from '../AssignedPersonnelSelection.vue'
import HaulersSelection from '../HaulersSelection.vue'

interface FifthSectionProps {
  canEdit?: boolean
  employees?: Employee[]
}

interface FifthSectionEmits {
  (e: 'loadEmployees'): void
}

const props = withDefaults(defineProps<FifthSectionProps>(), {
  canEdit: false,
})

const emit = defineEmits<FifthSectionEmits>()

const haulings = defineModel<Form3Hauling[]>('haulings', {
  default: () => [],
})

const isExistingHauler = (employeeId: number, index: number) => {
  if (haulings.value) {
    return haulings.value[index].haulers.map((h) => h.id).includes(employeeId)
  }
}

const removeHauler = (employee: Employee, index: number) => {
  const objIndex = haulings.value[index].haulers.findIndex(
    (h) => h.id === employee.id,
  )
  haulings.value[index].haulers.splice(objIndex, 1)
}

const handleHaulerMultiSelection = (employee: Employee, index: number) => {
  if (! props.canEdit) return

  if (isExistingHauler(employee.id, index)) {
    removeHauler(employee, index)

    toast(`Removed ${employee.fullName} from haulers`, {
      position: 'top-right',
      description: `on ${formatToDateString(haulings.value[index].date)} hauling`,
      action: {
        label: 'Undo',
        onClick: () => haulings.value[index].haulers.push(employee),
      },
    })
  } else {
    haulings.value[index].haulers.push(employee)
    toast(`Added ${employee.fullName} as hauler`, {
      position: 'top-right',
      description: `on ${formatToDateString(haulings.value[index].date)} hauling`,
      action: {
        label: 'Undo',
        onClick: () => removeHauler(employee, index),
      },
    })
  }
}

const removeExistingHaulers = (index: number) => {
  const temp = haulings.value[index].haulers

  haulings.value[index].haulers = []

  toast(`Removed all ${temp.length} haulers`, {
    position: 'top-right',
    description: `for ${formatToDateString(haulings.value[index].date)} hauling`,
    action: {
      label: 'Undo',
      onClick: () => (haulings.value[index].haulers = temp),
    },
  })
}

const HaulingRoles = [
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

type HaulingRoleType = (typeof HaulingRoles)[number]['id']

const handleAssignedPersonnelChanges = (
  role: HaulingRoleType,
  employee: Employee,
  index: number,
) => {
  haulings.value[index].assignedPersonnel[role] = employee
  isPopoverOpen.value[role] = null
}

const removeAssignedPersonnel = (role: HaulingRoleType, index: number) => {
  haulings.value[index].assignedPersonnel[role] = null as any
}

const isPopoverOpen = ref<Record<string, number | null>>({
  teamLeader: null,
  safetyOfficer: null,
  teamDriver: null,
  teamMechanic: null,
})

const loadEmployeesIfMissing = () => {
  if (props.employees === undefined) {
    emit('loadEmployees')
  }
}

const onPopoverToggle = (
  role: HaulingRoleType,
  index: number,
  value: boolean,
) => {
  isPopoverOpen.value[role] = value ? index : null
  loadEmployeesIfMissing()
}

const checklist = [
  {
    id: 'isVehicleInspectionFilled',
    description: 'Vehicle Inspection Form',
  },
  {
    id: 'isUniformPpeFilled',
    description: 'Uniform and PPE Request Form',
  },
  {
    id: 'isToolsEquipmentFilled',
    description: 'Tools and Equipment Request Form',
  },
] as const
</script>

<template>
  <div class="grid grid-cols-[auto,1fr] gap-y-6">
    <div v-if="haulings?.length">
      <div class="text-xl font-semibold leading-6">Assigned Personnel</div>
      <p class="text-sm text-muted-foreground">
        List of assigned personnel for each hauling operations.
      </p>
    </div>
    <Accordion
      type="multiple"
      class="col-[1/-1] w-full px-5"
      collapsible
    >
      <AccordionItem
        v-for="(hauling, index) in haulings"
        :key="hauling.id"
        :value="String(hauling.id)"
      >
        <div class="flex items-center rounded-sm bg-muted">
          <div class="flex-none pl-2">
            <Tooltip>
              <TooltipTrigger as-child>
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  class="rounded-full"
                >
                  <FilePenLine />
                </Button>
              </TooltipTrigger>
              <TooltipContent> Incident Report </TooltipContent>
            </Tooltip>
          </div>
          <div class="flex-none">
            <Dialog>
              <Tooltip>
                <TooltipTrigger as-child>
                  <DialogTrigger as-child>
                    <Button
                      type="button"
                      variant="ghost"
                      size="icon"
                      class="rounded-full"
                    >
                      <ClipboardCheck />
                    </Button>
                  </DialogTrigger>
                </TooltipTrigger>
                <TooltipContent> Safety Inspection Checklist </TooltipContent>
              </Tooltip>
              <DialogContent class="w-[450px]">
                <DialogHeader>
                  <DialogTitle> Safety Inspection </DialogTitle>
                  <DialogDescription class="mb-2">
                    Checklist filled-out by the Team Leader
                  </DialogDescription>
                </DialogHeader>
                <div class="flex flex-col gap-2">
                  <div
                    v-for="list in checklist"
                    :key="`${hauling.id}-${list.id}`"
                    class="flex items-center gap-x-3"
                  >
                    <Checkbox
                      :id="`${hauling.id}-${list.id}`"
                      :checked="Boolean(hauling.checklist[list.id])"
                      disabled
                    />
                    <Label
                      :for="`${hauling.id}-${list.id}`"
                      class="text-sm font-normal"
                    >
                      {{ list.description }}
                    </Label>
                  </div>
                </div>
                <Separator class="-mx-6 mt-3 w-[450px]" />
                <div
                  class="flex items-center justify-between text-xs text-muted-foreground"
                >
                  <div class="flex items-center gap-x-2">
                    <Avatar class="h-6 w-6 rounded-full">
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
                    <span class="truncate text-muted-foreground">
                      {{ hauling.assignedPersonnel.teamLeader.fullName }}
                    </span>
                  </div>
                  <div>{{ '3 days ago' }}</div>
                </div>
              </DialogContent>
            </Dialog>
          </div>
          <div class="flex-1 pl-3 pr-5">
            <AccordionTrigger>
              <div class="text-sm">
                {{ formatToDateString(hauling.date) }}
              </div>
            </AccordionTrigger>
          </div>
        </div>

        <AccordionContent
          class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-3 px-4 py-5"
        >
          <div class="col-span-2 grid grid-cols-2 gap-x-24">
            <AssignedPersonnelSelection
              :is-disabled="!canEdit"
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
              :is-disabled="!canEdit"
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
              :is-disabled="!canEdit"
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
              :is-disabled="!canEdit"
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
            <HaulersSelection
              :is-disabled="!canEdit"
              :employees="employees"
              :index="index"
              :selected-haulers="hauling.haulers"
              @on-hauler-select="handleHaulerMultiSelection"
              @on-remove-existing-haulers="removeExistingHaulers"
              @on-hauler-toggle="() => loadEmployeesIfMissing()"
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
                :disabled="!canEdit"
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
