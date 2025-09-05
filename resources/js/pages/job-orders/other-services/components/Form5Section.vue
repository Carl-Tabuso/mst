<script setup lang="ts">
import { ref, computed, watch } from 'vue'

interface Form5Item {
  item_name: string
  quantity: number
}

interface Employee {
  id: number
  name: string
}

interface Props {
  assignedPerson?: number | Employee | null
  items?: Form5Item[]
  employees?: Employee[] 
  purpose?: string | null 
  isEditing?: boolean 
}

const props = withDefaults(defineProps<Props>(), {
  assignedPerson: null,
  items: () => [],
  employees: () => [],
  purpose: null,
  isEditing: false 
})

const emit = defineEmits<{
  'update:assignedPerson': [value: number | null]
  'update:items': [value: Form5Item[]]
  'update:purpose': [value: string | null] 
}>()

const assignedPersonId = computed({
  get: () => {
    if (!props.assignedPerson) return null
    return typeof props.assignedPerson === 'object' 
      ? props.assignedPerson.id 
      : props.assignedPerson
  },
  set: (value) => emit('update:assignedPerson', value)
})

const purpose = computed({
  get: () => props.purpose,
  set: (value) => emit('update:purpose', value)
})

const localItems = ref<Form5Item[]>([...props.items])

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

watch(localItems, (newItems) => {
  emit('update:items', newItems)
}, { deep: true })

if (localItems.value.length === 0) {
  localItems.value.push({ item_name: '', quantity: 1 })
}

const addItem = () => {
  localItems.value.push({ item_name: '', quantity: 1 })
}

const removeItem = (index: number) => {
  if (localItems.value.length > 1) {
    localItems.value.splice(index, 1)
  } else {
    localItems.value[0] = { item_name: '', quantity: 1 }
  }
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

    <!-- Assigned Person and Purpose in same row -->
    <div class="grid grid-cols-2 gap-x-10 gap-y-3 items-center">
      <!-- Assigned Person -->
      <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 items-center">
        <label class="text-sm font-medium">Assigned Person</label>
        <div class="w-full">
          <select 
            v-model="assignedPersonId" 
            :disabled="!isEditing"
            class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
          >
            <option :value="null">Select assigned person</option>
            <option 
              v-for="employee in props.employees" 
              :key="employee.id" 
              :value="employee.id"
            >
              {{ employee.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Purpose -->
      <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 items-center">
        <label class="text-sm font-medium">Purpose</label>
        <div class="w-full">
          <select 
            v-model="purpose" 
            :disabled="!isEditing"
            class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
          >
            <option :value="null">Select purpose</option>
            <option 
              v-for="option in purposeOptions" 
              :key="option" 
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Items Section -->
    <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
      <label class="text-sm font-medium self-start pt-2">Items</label>
      <div class="space-y-3">
        <div 
          v-for="(item, index) in localItems" 
          :key="index" 
          class="flex items-center gap-3"
        >
          <input
            v-model="item.item_name"
            :disabled="!isEditing"
            placeholder="Item name"
            class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
            required
          />
          <input
            v-model.number="item.quantity"
            type="number"
            min="1"
            :disabled="!isEditing"
            placeholder="Qty"
            class="w-20 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
            required
          />
          <button
            v-if="isEditing"
            type="button"
            @click="removeItem(index)"
            class="px-3 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': localItems.length <= 1 }"
            :disabled="localItems.length <= 1"
          >
            Remove
          </button>
        </div>
        
        <button
          v-if="isEditing"
          type="button"
          @click="addItem"
          class="mt-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors"
        >
          + Add Another Item
        </button>

        <!-- Display items in read-only mode -->
        <div v-if="!isEditing && localItems.length > 0" class="mt-4">
          <div v-for="(item, index) in localItems" :key="index" class="flex justify-between py-2 border-b">
            <span class="font-medium">{{ item.item_name || 'Unnamed item' }}</span>
            <span class="text-gray-600">Qty: {{ item.quantity }}</span>
          </div>
        </div>

        <div v-if="!isEditing && localItems.length === 0" class="text-gray-500 italic">
          No items added
        </div>
      </div>
    </div>
  </div>
</template>