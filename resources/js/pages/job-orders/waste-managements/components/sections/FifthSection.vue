<script setup lang="ts">
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { formatToDateString } from '@/composables/useDateFormatter'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { haulingRoles, HaulingRoleType } from '@/constants/hauling-role'
import {
  haulingStatuses,
  HaulingStatusType,
} from '@/constants/hauling-statuses'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Employee, Form3Hauling } from '@/types'
import { isToday } from 'date-fns'
import { FilePenLine } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'
import AssignedPersonnelSelection from '../AssignedPersonnelSelection.vue'
import FormAreaInfo from '../FormAreaInfo.vue'
import HaulersSelection from '../HaulersSelection.vue'
import SafetyInspectionChecklist from '../SafetyInspectionChecklist.vue'
import SectionButton from '../SectionButton.vue'

interface FifthSectionProps {
  status: JobOrderStatus
  employees?: Employee[]
  isSubmitBtnDisabled: boolean
}

interface FifthSectionEmits {
  (e: 'loadEmployees'): void
  (e: 'onCancelSubmit'): void
}

const props = withDefaults(defineProps<FifthSectionProps>(), {
  isSubmitBtnDisabled: false,
})

const emit = defineEmits<FifthSectionEmits>()

const { can } = usePermissions()

const isAuthorize = computed(() => can('assign:hauling_personnel'))

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
  const hauling = haulings.value[index]

  if (!isAuthorize.value || !hauling.isOpen) return

  if (isExistingHauler(employee.id, index)) {
    removeHauler(employee, index)

    toast(`Removed ${employee.fullName} from haulers`, {
      position: 'top-right',
      description: `on ${formatToDateString(hauling.date)} hauling`,
      action: {
        label: 'Undo',
        onClick: () => hauling.haulers.push(employee),
      },
    })
  } else {
    hauling.haulers.push(employee)
    toast(`Added ${employee.fullName} as hauler`, {
      position: 'top-right',
      description: `on ${formatToDateString(hauling.date)} hauling`,
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

const { isHaulingInProgress } = useWasteManagementStages()

const isHauling = computed(() => isHaulingInProgress(props.status))
</script>

<template>
  <FormAreaInfo
    :condition="isHauling"
    class="mb-4"
  >
    <span class="pr-1 font-semibold">Dispatcher</span>is required to assign the
    personnel and haulers daily during the duration of hauling period.
  </FormAreaInfo>
  <div class="grid grid-cols-[auto,1fr] gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6">Assigned Personnel</div>
      <p class="text-sm text-muted-foreground">
        List of assigned personnel for each hauling operations.
      </p>
    </div>
    <Accordion
      type="multiple"
      class="col-[1/-1] w-full rounded-sm border"
      collapsible
    >
      <AccordionItem
        v-for="(hauling, index) in haulings"
        :key="hauling.id"
        :value="String(hauling.id)"
      >
        <div
          :class="[
            'rounded- flex items-center',
            { 'bg-muted': isToday(new Date(hauling.date)) },
          ]"
        >
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
            <SafetyInspectionChecklist :hauling="hauling" />
          </div>
          <div class="flex-1 pl-3 pr-5">
            <AccordionTrigger>
              <div class="flex items-center gap-3">
                <div class="text-sm">
                  {{ formatToDateString(hauling.date) }}
                </div>
                <span class="no-underline hover:no-underline">
                  <Badge
                    :variant="
                      haulingStatuses.find((hs) => hs.id === hauling.status)
                        ?.badge
                    "
                  >
                    {{
                      haulingStatuses.find((hs) => hs.id === hauling.status)
                        ?.label
                    }}
                  </Badge>
                </span>
              </div>
            </AccordionTrigger>
          </div>
        </div>

        <AccordionContent
          class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-3 px-4 py-5"
        >
          <div class="col-span-2 grid grid-cols-2 gap-3 gap-x-24">
            <AssignedPersonnelSelection
              v-for="haulingRole in haulingRoles"
              :key="`${hauling.id}-${haulingRole.id}`"
              :can-edit="
                isAuthorize && hauling.isOpen && hauling.status !== 'in progress'
              "
              :employees="employees"
              :hauling="hauling"
              :role="haulingRole.id"
              :index="index"
              :label="haulingRole.label"
              :open="isPopoverOpen[haulingRole.id] === index"
              @toggled="onPopoverToggle"
              @clicked="handleAssignedPersonnelChanges"
              @removed="removeAssignedPersonnel"
            />
          </div>
          <div class="col-span-2 grid grid-cols-2 gap-x-24">
            <HaulersSelection
              :is-authorize="isAuthorize && hauling.status !== 'in progress'"
              :hauling="hauling"
              :employees="employees"
              :index="index"
              @on-hauler-select="handleHaulerMultiSelection"
              @on-remove-existing-haulers="removeExistingHaulers"
              @on-hauler-toggle="loadEmployeesIfMissing"
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
                :disabled="
                  !isAuthorize ||
                  !hauling.isOpen ||
                  hauling.status === 'in progress'
                "
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
  <div
    v-if="isAuthorize && isHauling"
    class="mt-6 flex justify-end"
  >
    <SectionButton
      :is-submit-btn-disabled="isSubmitBtnDisabled"
      @on-cancel-submit="$emit('onCancelSubmit')"
    />
  </div>
</template>
