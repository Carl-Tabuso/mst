<script setup lang="ts">
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { haulingRoles, HaulingRoleType } from '@/constants/hauling-role'
import { haulingStatuses } from '@/constants/hauling-statuses'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { UserRoleType } from '@/constants/user-role'
import { Employee, Form3Hauling } from '@/types'
import { Link, useForm } from '@inertiajs/vue3'
import { format, isToday } from 'date-fns'
import { FilePenLine } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import { GroupedEmployeesByAccountRole } from '../..'
import AssignedPersonnelSelection from '../AssignedPersonnelSelection.vue'
import FormAreaInfo from '../FormAreaInfo.vue'
import HaulersSelection from '../HaulersSelection.vue'
import SafetyInspectionChecklist from '../SafetyInspectionChecklist.vue'
import SectionButton from '../SectionButton.vue'
import TruckSelection from '../TruckSelection.vue'

interface FifthSectionProps {
  status: JobOrderStatus
  groupedEmployees?: GroupedEmployeesByAccountRole[]
  haulings: Form3Hauling[]
  serviceableId: number
}

interface FifthSectionEmits {
  (e: 'loadEmployees'): void
  (e: 'onCancelSubmit'): void
}

const props = defineProps<FifthSectionProps>()

const emit = defineEmits<FifthSectionEmits>()

const { can } = usePermissions()

const isAuthorize = computed(() => can('assign:hauling_personnel'))

const trackedHaulings = ref<Form3Hauling[]>(props.haulings)

watch(
  () => props.haulings,
  (newValue) => {
    trackedHaulings.value = newValue
  },
)

const isExistingHauler = (employeeId: number, index: number) => {
  return trackedHaulings.value[index].haulers
    .map((h) => h.id)
    .includes(employeeId)
}

const removeHauler = (employee: Employee, index: number) => {
  const objIndex = trackedHaulings.value[index].haulers.findIndex(
    (h) => h.id === employee.id,
  )
  trackedHaulings.value[index].haulers.splice(objIndex, 1)
}

const handleHaulerMultiSelection = (employee: Employee, index: number) => {
  const hauling = trackedHaulings.value[index]

  if (!isAuthorize.value || !hauling.isOpen) return

  if (isExistingHauler(employee.id, index)) {
    removeHauler(employee, index)
  } else {
    hauling.haulers.push(employee)
  }
}

const removeExistingHaulers = (index: number) => {
  trackedHaulings.value[index].haulers = []
}

const handleAssignedPersonnelChanges = (
  role: HaulingRoleType,
  employee: Employee,
  index: number,
) => {
  trackedHaulings.value[index].assignedPersonnel[role] = employee
  isPopoverOpen.value[role] = null
}

const removeAssignedPersonnel = (role: HaulingRoleType, index: number) => {
  trackedHaulings.value[index].assignedPersonnel[role] = null as any
}

const isPopoverOpen = ref<Record<string, number | null>>({
  teamLeader: null,
  safetyOfficer: null,
  teamDriver: null,
  teamMechanic: null,
})

const loadEmployeesIfMissing = () => {
  if (props.groupedEmployees === undefined) {
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

const { isInProgress } = useWasteManagementStages()

const isHauling = computed(() => isInProgress(props.status))

const form = useForm({
  status: props.status,
})

const onSubmit = () => {
  form
    .transform((data) => ({
      ...data,
      haulings: trackedHaulings.value,
    }))
    .patch(route('job_order.waste_management.update', props.serviceableId), {
      preserveScroll: true,
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message)
      },
    })
}

const filterByUserRole = (roles: UserRoleType | UserRoleType[]) => {
  return props.groupedEmployees
    ?.filter((group) => {
      if (Array.isArray(roles)) {
        return roles.includes(group.role)
      }
      return group.role === roles
    })
    .flatMap((filtered) => filtered.items)
}
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
        v-for="(hauling, index) in trackedHaulings"
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
                <Link
                  :href="route('incidents.index', { hauling_id: hauling.id })"
                >
                  <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="rounded-full"
                  >
                    <FilePenLine />
                  </Button>
                </Link>
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
                  {{ format(hauling.date, 'EEEE, MMMM d, yyyy') }}
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
              :can-edit="isAuthorize && hauling.isOpen"
              :employees="filterByUserRole(haulingRole.type)"
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
              :is-authorize="isAuthorize"
              :hauling="hauling"
              :employees="filterByUserRole('regular')"
              :index="index"
              @on-hauler-select="handleHaulerMultiSelection"
              @on-remove-existing-haulers="removeExistingHaulers"
              @on-hauler-toggle="loadEmployeesIfMissing"
            />
            <!-- <div class="flex items-center gap-x-10">
              <Label
                :for="'truckNo-' + hauling.id"
                class="w-28 shrink-0"
              >
                Truck Number
              </Label>
              <Input
                :id="'truckNo-' + hauling.id"
                :disabled="!isAuthorize || !hauling.isOpen"
                placeholder="Enter truck plate number"
                v-model="hauling.truckNo"
                class="w-[400px]"
              />
            </div> -->
            <TruckSelection
              v-model:truck="hauling.truck"
              :can-edit="isAuthorize && hauling.isOpen"
            />
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
      :is-submit-btn-disabled="form.processing"
      @on-submit="onSubmit"
      @on-cancel-submit="form.cancel()"
    />
  </div>
</template>
