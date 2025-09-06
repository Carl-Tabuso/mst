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

const serviceableChangedFields = Object.keys(props.changes.after?.serviceable || {})
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
      class: ''
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
  'Bid Bond Submission'
]

const getAssignedPersonName = () => {
  const assignedPerson = form5Data?.assigned_person || form5Data?.assigned_person_id
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
  <div class="mt-8 pt-6 border-t border-gray-200 space-y-6">
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Form 5 Details</div>
      <p class="text-sm text-muted-foreground">
        Additional information required for Form 5 job orders.
      </p>
    </div>

    <div class="grid grid-cols-2 gap-x-10 gap-y-3 items-center">
      <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 items-center">
        <Label class="text-sm font-medium">Assigned Person</Label>
        <Input
          :value="getAssignedPersonName()"
          :class="wasServiceableChanged('assigned_person').class"
          class="pointer-events-none"
        />
      </div>

      <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 items-center">
        <Label class="text-sm font-medium">Purpose</Label>
        <Input
          :value="getPurposeDisplay()"
          :class="wasServiceableChanged('purpose').class"
          class="pointer-events-none"
        />
      </div>
    </div>

    <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
      <Label class="text-sm font-medium self-start pt-2">Items</Label>
      <div class="space-y-3">
        <div 
          v-if="form5Data?.items && form5Data.items.length > 0"
          class="space-y-2"
        >
          <div 
            v-for="(item, index) in form5Data.items" 
            :key="index" 
            class="flex items-center gap-3 p-2 border rounded-md"
            :class="serviceableChangedFields.includes('items') ? 'bg-amber-50 border-warning' : ''"
          >
            <Input
              :value="item.item_name || 'Unnamed item'"
              class="pointer-events-none flex-1 bg-transparent border-none"
            />
            <Input
              :value="`Qty: ${item.quantity}`"
              class="pointer-events-none w-20 bg-transparent border-none"
            />
          </div>
        </div>
        <div 
          v-else 
          class="text-gray-500 italic p-2"
        >
          No items added
        </div>

        <div 
          v-if="serviceableChangedFields.includes('items') && serviceableAfter.items && serviceableAfter.items.length > 0"
          class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md"
        >
          <Label class="text-green-700 font-medium">Proposed Changes:</Label>
          <div class="space-y-2 mt-2">
            <div 
              v-for="(item, index) in serviceableAfter.items" 
              :key="index" 
              class="flex items-center gap-3 p-2 border border-green-300 rounded-md bg-white"
            >
              <Input
                :value="item.item_name || 'Unnamed item'"
                class="pointer-events-none flex-1 bg-transparent border-none"
              />
              <Input
                :value="`Qty: ${item.quantity}`"
                class="pointer-events-none w-20 bg-transparent border-none"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>