<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { usePermissions } from '@/composables/usePermissions'
import {
  ChecklistItemType,
  safetyInspectionChecklist,
} from '@/constants/safety-inspection-checklist'
import { Form3Hauling } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import {
  ClipboardCheck,
  LoaderCircle,
  TriangleAlert,
  UserRound,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'

interface SafetyInspectionChecklistProps {
  hauling: Form3Hauling
}

const { hauling } = defineProps<SafetyInspectionChecklistProps>()

const currentChecklist = ref({
  isVehicleInspectionFilled: hauling.checklist.isVehicleInspectionFilled,
  isUniformPpeFilled: hauling.checklist.isUniformPpeFilled,
  isToolsEquipmentFilled: hauling.checklist.isToolsEquipmentFilled,
  isCertified: hauling.checklist.isCertified,
})

const handleListSelection = (value: boolean, listItem: ChecklistItemType) => {
  currentChecklist.value[listItem] = value
}

const form = useForm<any>({})

const onFormSubmit = (hauling: Form3Hauling) => {
  form
    .transform(() => ({
      is_vehicle_inspection_filled:
        currentChecklist.value.isVehicleInspectionFilled,
      is_uniform_ppe_filled: currentChecklist.value.isUniformPpeFilled,
      is_tools_equipment_filled: currentChecklist.value.isToolsEquipmentFilled,
      is_certify: currentChecklist.value.isCertified,
    }))
    .patch(
      route(
        'job_order.waste_management.safety_inspection.update',
        hauling.checklist.id,
      ),
      {
        preserveScroll: true,
        showProgress: false,
        onStart: () => form.clearErrors(),
        onSuccess: (page: any) => {
          form.reset()
          isDialogOpen.value = false

          const { title, description } = page.props.flash.message
          toast.success(title, {
            description: `${format(description, 'PPPP')}`,
          })
        },
      },
    )
}

const isDialogOpen = ref<boolean>(false)

const { can } = usePermissions()

const canEdit = computed(() => {
  return (
    can('fill:safety_inspection_checklist') &&
    hauling.status === 'for safety inspection' &&
    hauling.isOpen
  )
})

const teamLeader = computed(() => hauling?.assignedPersonnel?.teamLeader)
const day = format(hauling.date, 'ccc')
const date = format(hauling.date, 'MMM d')
</script>

<template>
  <Dialog v-model:open="isDialogOpen">
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
    <DialogContent class="max-w-[700px]">
      <DialogHeader class="flex">
        <DialogTitle class="flex items-center gap-3 text-2xl font-bold">
          <div>Safety Inspection Checklist</div>
          <span>•</span>
          <div>{{ `${day}, ${date}` }}</div>
        </DialogTitle>
        <DialogDescription class="text-sm leading-4">
          Review all items and ensure that all necessary forms have been
          completed. Your certification will serve as your signature.
        </DialogDescription>
      </DialogHeader>
      <div class="flex flex-col gap-6">
        <div class="mt-3 flex flex-col gap-2">
          <div
            v-for="(list, index) in safetyInspectionChecklist"
            :key="`${hauling.id}-${list.id}`"
            :class="[
              'flex items-center gap-x-3',
              { 'mt-4': index === safetyInspectionChecklist.length - 1 },
            ]"
          >
            <Checkbox
              :id="`${hauling.id}-${list.id}`"
              :checked="Boolean(currentChecklist[list.id])"
              @update:checked="(value) => handleListSelection(value, list.id)"
              :disabled="!canEdit || form.processing"
            />
            <Label
              :for="`${hauling.id}-${list.id}`"
              class="text-sm"
            >
              {{ list.description }}
            </Label>
          </div>
          <InputError :message="form.errors.is_certify" />
        </div>
        <Alert
          v-show="canEdit"
          variant="warning"
        >
          <TriangleAlert class="h-4 w-4" />
          <AlertTitle> Warning </AlertTitle>
          <AlertDescription>
            Once all checkboxes are selected and submitted, this date will be
            marked In-Progress and you won’t be able to make changes.
          </AlertDescription>
        </Alert>
        <div :class="[{ 'flex justify-start': !canEdit }]">
          <DialogFooter class="mt-3 flex">
            <div
              v-if="hauling.isOpen"
              class="flex items-center gap-3"
            >
              <Button
                v-if="canEdit"
                type="submit"
                :disabled="form.processing"
                @click="onFormSubmit(hauling)"
              >
                <LoaderCircle
                  v-show="form.processing"
                  class="animate-spin"
                />
                {{
                  Object.entries(currentChecklist).every(
                    ([key, value]) => value,
                  )
                    ? 'Mark as In-Progress'
                    : 'Save & Submit'
                }}
              </Button>
            </div>
          </DialogFooter>
          <div
            v-if="!canEdit"
            class="mt-2 flex items-center text-[13px] font-medium text-muted-foreground"
          >
            <UserRound
              :size="16"
              class="mr-2"
            />
            Team Leader: {{ teamLeader ? teamLeader.fullName : 'Not Assigned' }}
          </div>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
