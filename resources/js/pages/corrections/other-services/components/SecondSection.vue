<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useCorrections } from '@/composables/useCorrections'
import { JobOrder } from '@/types'
import { computed } from 'vue'

interface SecondSectionProps {
  jobOrder: JobOrder
  changes: any
}

const props = defineProps<SecondSectionProps>()

const { getNestedObject } = useCorrections()




const hasServiceableChanges = computed(() => {
  return props.changes.before && props.changes.after
})

const serviceableChangedFields = computed(() => {
  if (!hasServiceableChanges.value) return []
  return Object.keys(props.changes.after)
})

const serviceableBefore = computed(() => props.changes.before || {})
const serviceableAfter = computed(() => props.changes.after || {})


const getAssignedPersonDisplay = (value: any) => {
  if (!value) return 'Not assigned'
  
  if (typeof value === 'object') {
    return value.name || value.full_name || 'Unknown'
  }
  return `Employee #${value}`
}

const getCurrentAssignedPerson = () => {
  const assignedPerson = props.jobOrder.serviceable?.assigned_person
  return getAssignedPersonDisplay(assignedPerson)
}

const getChangedAssignedPerson = () => {
  if (serviceableChangedFields.value.includes('assigned_person')) {
    return getAssignedPersonDisplay(serviceableAfter.value.assigned_person)
  }
  return getCurrentAssignedPerson()
}

const getAssignedPersonClass = () => {
  if (serviceableChangedFields.value.includes('assigned_person')) {
    return 'bg-amber-50 border-warning dark:bg-transparent pointer-events-none'
  }
  return 'pointer-events-none'
}

const getPurposeValue = () => {
  if (serviceableChangedFields.value.includes('purpose')) {
    return serviceableAfter.value.purpose
  }
  return props.jobOrder.serviceable?.purpose
}

const getPurposeClass = () => {
  if (serviceableChangedFields.value.includes('purpose')) {
    return 'bg-amber-50 border-warning dark:bg-transparent pointer-events-none'
  }
  return 'pointer-events-none'
}

const getDisplayItems = () => {
  if (serviceableChangedFields.value.includes('items') && serviceableAfter.value.items) {
    return serviceableAfter.value.items
  }
  return props.jobOrder.serviceable?.items || []
}

const itemsChanged = computed(() => serviceableChangedFields.value.includes('items'))
</script>

<template>
  <div class="my-4 flex flex-col gap-4 rounded-xl">
    <div class="mb-3 flex items-center">
      <div class="flex w-full flex-col">
        <div class="grid gap-y-6">
          <div>
            <h4 class="text-xl font-semibold leading-6 text-foreground">
              Form 5 Details
            </h4>
            <p class="text-sm text-muted-foreground">
              Additional information required for Form 5 job orders.
            </p>
          </div>
          
          <div class="grid grid-cols-2 gap-x-10 gap-y-3">
            <div class="flex items-center gap-x-4">
              <Label class="w-44 shrink-0">Assigned Person</Label>
              <div class="w-full">
                <Input
                  type="text"
                  :model-value="getChangedAssignedPerson()"
                  :class="getAssignedPersonClass()"
                  class="pointer-events-none w-full"
                />
                <p v-if="serviceableChangedFields.includes('assigned_person')" class="text-xs text-muted-foreground mt-1">
                  Changed from: {{ getAssignedPersonDisplay(serviceableBefore.assigned_person) }}
                </p>
              </div>
            </div>

            <div class="flex items-center">
              <Label class="w-36 shrink-0">Purpose</Label>
              <div class="w-full">
                <Input
                  type="text"
                  :model-value="getPurposeValue()"
                  :class="getPurposeClass()"
                  class="pointer-events-none w-full"
                />
                <p v-if="serviceableChangedFields.includes('purpose')" class="text-xs text-muted-foreground mt-1">
                  Changed from: {{ serviceableBefore.purpose }}
                </p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
            <Label class="self-start pt-2">Items</Label>
            <div class="space-y-3">
              <div 
                v-if="getDisplayItems().length > 0"
                class="space-y-2"
              >
                <div 
                  v-for="(item, index) in getDisplayItems()" 
                  :key="index" 
                  class="flex items-center gap-3 p-2 border rounded-md"
                  :class="{'border-warning': itemsChanged}"
                >
                  <Input
                    :model-value="item.item_name || 'Unnamed item'"
                    class="pointer-events-none flex-1"
                    :class="{'bg-amber-50': itemsChanged}"
                  />
                  <Input
                    :model-value="`Qty: ${item.quantity}`"
                    class="pointer-events-none w-20"
                    :class="{'bg-amber-50': itemsChanged}"
                  />
                </div>
              </div>
              <div 
                v-else 
                class="text-gray-500 italic p-2"
              >
                No items added
              </div>
            </div>
          </div>

          <div 
            v-if="itemsChanged && serviceableBefore.items && serviceableBefore.items.length > 0"
            class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 bg-red-50 p-3 rounded-md border border-red-200"
          >
            <Label class="self-start pt-2 text-red-700">Original Items</Label>
            <div class="space-y-3">
              <div class="space-y-2">
                <div 
                  v-for="(item, index) in serviceableBefore.items" 
                  :key="index" 
                  class="flex items-center gap-3 p-2 border rounded-md border-red-200"
                >
                  <Input
                    :model-value="item.item_name || 'Unnamed item'"
                    class="flex-1 bg-red-100 border-red-200 pointer-events-none"
                  />
                  <Input
                    :model-value="`Qty: ${item.quantity}`"
                    class="w-20 bg-red-100 border-red-200 pointer-events-none"
                  />
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>
</template>