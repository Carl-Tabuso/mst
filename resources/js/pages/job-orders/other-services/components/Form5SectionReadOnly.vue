<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { JobOrder } from '@/types'

interface Props {
  jobOrder: JobOrder
  changes: any
}

const props = defineProps<Props>()

const form5Data = props.jobOrder.serviceable

const serviceableChangedFields = Object.keys(
  props.changes.after?.serviceable || {},
)
const serviceableAfter = props.changes.after?.serviceable || {}

const wasServiceableChanged = (field: string) => {
  if (serviceableChangedFields.includes(field)) {
    return {
      defaultValue: serviceableAfter[field],
      class: 'bg-amber-50 border-warning dark:bg-transparent',
    }
  } else {
    const currentValue = form5Data?.[field]
    return {
      defaultValue: currentValue,
      class: '',
    }
  }
}

const purposeOptions = [
  'Payment',
  'Process and Pick Up Document',
  'Bank Transaction',
  'Document Delivery',
  'Document Pickup',
  'Bid Bond Pickup',
  'Payment Transaction',
  'Bid Bond Submission',
]

const getAssignedPersonName = () => {
  const assignedPerson =
    form5Data?.assigned_person || form5Data?.assigned_person_id
  if (!assignedPerson) return 'Not assigned'

  if (typeof assignedPerson === 'object') {
    return assignedPerson.name || 'Unknown'
  }

  return `Employee #${assignedPerson}`
}

const getPurposeDisplay = () => {
  const purpose = wasServiceableChanged('purpose').defaultValue
  return purpose || 'Not specified'
}
</script>

<template>
  <div class="mt-8 space-y-6 border-t border-gray-200 pt-6">
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Form 5 Details</div>
      <p class="text-sm text-muted-foreground">
        Additional information required for Form 5 job orders.
      </p>
    </div>

    <div class="grid grid-cols-2 items-center gap-x-10 gap-y-3">
      <div class="grid grid-cols-[auto,1fr] items-center gap-x-7 gap-y-3">
        <Label class="text-sm font-medium">Assigned Person</Label>
        <Input
          :value="getAssignedPersonName()"
          :class="wasServiceableChanged('assigned_person').class"
          class="pointer-events-none"
        />
      </div>

      <div class="grid grid-cols-[auto,1fr] items-center gap-x-7 gap-y-3">
        <Label class="text-sm font-medium">Purpose</Label>
        <Input
          :value="getPurposeDisplay()"
          :class="wasServiceableChanged('purpose').class"
          class="pointer-events-none"
        />
      </div>
    </div>

    <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
      <Label class="self-start pt-2 text-sm font-medium">Items</Label>
      <div class="space-y-3">
        <div
          v-if="form5Data?.items && form5Data.items.length > 0"
          class="space-y-2"
        >
          <div
            v-for="(item, index) in form5Data.items"
            :key="index"
            class="flex items-center gap-3 rounded-md border p-2"
            :class="
              serviceableChangedFields.includes('items')
                ? 'border-warning bg-amber-50'
                : ''
            "
          >
            <Input
              :value="item.item_name || 'Unnamed item'"
              class="pointer-events-none flex-1 border-none bg-transparent"
            />
            <Input
              :value="`Qty: ${item.quantity}`"
              class="pointer-events-none w-20 border-none bg-transparent"
            />
          </div>
        </div>
        <div
          v-else
          class="p-2 italic text-gray-500"
        >
          No items added
        </div>

        <div
          v-if="
            serviceableChangedFields.includes('items') &&
            serviceableAfter.items &&
            serviceableAfter.items.length > 0
          "
          class="mt-4 rounded-md border border-green-200 bg-green-50 p-3"
        >
          <Label class="font-medium text-green-700">Proposed Changes:</Label>
          <div class="mt-2 space-y-2">
            <div
              v-for="(item, index) in serviceableAfter.items"
              :key="index"
              class="flex items-center gap-3 rounded-md border border-green-300 bg-white p-2"
            >
              <Input
                :value="item.item_name || 'Unnamed item'"
                class="pointer-events-none flex-1 border-none bg-transparent"
              />
              <Input
                :value="`Qty: ${item.quantity}`"
                class="pointer-events-none w-20 border-none bg-transparent"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
