<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  CorrectionFieldKey,
  useCorrections,
} from '@/composables/useCorrections'
import { formatToDateDisplay } from '@/composables/useDateFormatter'
import { usePermissions } from '@/composables/usePermissions'
import { CorrectionStatusType } from '@/constants/correction-statuses'
import { type Employee } from '@/types' // Import Employee type
import { CircleArrowRight, FileClock } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import ConfirmStatus from './ConfirmStatus.vue'

interface ChangesModalProps {
  changes: any
  status: CorrectionStatusType
  employees?: Employee[]
}

const props = defineProps<ChangesModalProps>()

const isDialogOpen = ref<boolean>(false)

const { before, after } = props.changes

const { fieldMap, isDateString } = useCorrections()

const getEmployeeName = (employeeId: number | string | null) => {
  if (!employeeId) return 'Not assigned'

  const id =
    typeof employeeId === 'string' ? parseInt(employeeId, 10) : employeeId

  if (props.employees && props.employees.length > 0) {
    const employee = props.employees.find((emp) => emp.id === id)
    return employee ? employee.fullName : `Employee #${id}`
  }

  return `Employee #${id}`
}

const formatAssignedPerson = (value: any) => {
  if (!value) return 'Not assigned'

  if (typeof value === 'object') {
    return value.fullName || value.name || 'Unknown'
  }

  return getEmployeeName(value)
}

const regularChanges = computed(() => {
  const changes = []

  for (const key in before) {
    if (key === 'serviceable') continue

    if (fieldMap[key as CorrectionFieldKey]) {
      changes.push({
        field: fieldMap[key as CorrectionFieldKey].label,
        oldValue: isDateString(key as CorrectionFieldKey)
          ? formatToDateDisplay(before[key], 'MMMM d, yyyy')
          : before[key],
        newValue: isDateString(key as CorrectionFieldKey)
          ? formatToDateDisplay(after[key], 'MMMM d, yyyy')
          : after[key],
      })
    } else {
      changes.push({
        field: key.replace(/_/g, ' ').toUpperCase(),
        oldValue: before[key] ?? 'N/A',
        newValue: after[key] ?? 'N/A',
      })
    }
  }

  return changes
})

const serviceableChanges = computed(() => {
  const changes = []

  if (after?.serviceable) {
    for (const field in after.serviceable) {
      if (field === 'items') {
        const oldItems = before?.serviceable?.items || []
        const newItems = after?.serviceable?.items || []

        changes.push({
          field: 'Service Items',
          oldValue:
            oldItems.length > 0
              ? oldItems
                  .map(
                    (item: { item_name: string; quantity: number }) =>
                      `${item.item_name} (Qty: ${item.quantity})`,
                  )
                  .join(', ')
              : 'No items',
          newValue:
            newItems.length > 0
              ? newItems
                  .map(
                    (item: { item_name: string; quantity: number }) =>
                      `${item.item_name} (Qty: ${item.quantity})`,
                  )
                  .join(', ')
              : 'No items',
        })
      } else if (field === 'assigned_person') {
        changes.push({
          field: 'Assigned Person',
          oldValue: formatAssignedPerson(before?.serviceable?.[field]),
          newValue: formatAssignedPerson(after?.serviceable?.[field]),
        })
      } else {
        changes.push({
          field: `Service - ${field.replace('_', ' ').toUpperCase()}`,
          oldValue: before?.serviceable?.[field] || 'N/A',
          newValue: after?.serviceable?.[field] || 'N/A',
        })
      }
    }
  }

  return changes
})

const allChanges = computed(() => [
  ...regularChanges.value,
  ...serviceableChanges.value,
])
const { can } = usePermissions()

const isApprovable =
  props.status === 'pending' && can('update:job_order_correction')
</script>

<template>
  <Dialog v-model="isDialogOpen">
    <DialogTrigger>
      <Button
        variant="link"
        class="text-yellow-500 dark:text-warning"
      >
        <FileClock class="stroke-2" />
        See Changes
      </Button>
    </DialogTrigger>
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle class="text-yellow-500 dark:text-warning">
          Viewing Changes
        </DialogTitle>
        <DialogDescription>
          Here are the previous changes with the new corrections. Review the
          changes below and decide whether to accept or reject the corrections.
        </DialogDescription>
      </DialogHeader>
      <div class="my-2 flex max-h-[60dvh] flex-col overflow-y-auto">
        <div
          v-for="{ field, oldValue, newValue } in allChanges"
          :key="field"
          class="py-1"
        >
          <div class="mb-1 text-xs font-medium text-muted-foreground">
            {{ field }}
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div
              class="rounded-md bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/30 dark:text-red-300"
            >
              {{ oldValue }}
            </div>
            <div
              class="rounded-md bg-green-50 px-3 py-1 text-green-700 dark:bg-green-950/30 dark:text-green-300"
            >
              {{ newValue }}
            </div>
          </div>
        </div>
      </div>
      <DialogFooter>
        <DialogClose>
          <Button variant="outline"> Close </Button>
        </DialogClose>
        <Dialog>
          <DialogTrigger :disabled="!isApprovable">
            <Button
              variant="warning"
              :class="{ 'cursor-not-allowed opacity-50': !isApprovable }"
            >
              <CircleArrowRight class="mr-1 stroke-2" />
              Proceed to Approval
            </Button>
          </DialogTrigger>
          <ConfirmStatus
            :new-values="changes.after"
            @on-status-update="isDialogOpen = false"
          />
        </Dialog>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
