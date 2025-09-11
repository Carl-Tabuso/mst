<script setup lang="ts">
import { type Employee } from '@/types'

interface Form5Item {
  item_name: string
  quantity: number
}

interface Props {
  employees?: Employee[]
  isEditing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  employees: () => [],
  isEditing: false,
})

const assignedPerson = defineModel<number | null>('assignedPerson', {
  required: true,
})
const items = defineModel<Form5Item[]>('items', {
  required: true,
  default: () => [],
})
const purpose = defineModel<string>('purpose', { required: true, default: '' })

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

const addItem = () => {
  items.value = [...items.value, { item_name: '', quantity: 1 }]
}

const removeItem = (index: number) => {
  if (items.value.length > 1) {
    items.value = items.value.filter((_, i) => i !== index)
  } else if (props.isEditing) {
    items.value = [{ item_name: '', quantity: 1 }]
  }
}

if (items.value.length === 0 && props.isEditing) {
  addItem()
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

    <!-- Assigned Person and Purpose in same row -->
    <div class="grid grid-cols-2 items-center gap-x-10 gap-y-3">
      <!-- Assigned Person -->
      <div class="grid grid-cols-[auto,1fr] items-center gap-x-7 gap-y-3">
        <label class="text-sm font-medium">Assigned Person</label>
        <div class="w-full">
          <select
            v-model="assignedPerson"
            :disabled="!isEditing"
            class="w-full rounded-md border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
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
      <div class="grid grid-cols-[auto,1fr] items-center gap-x-7 gap-y-3">
        <label class="text-sm font-medium">Purpose</label>
        <div class="w-full">
          <select
            v-model="purpose"
            :disabled="!isEditing"
            class="w-full rounded-md border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
          >
            <option value="">Select purpose</option>
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
      <label class="self-start pt-2 text-sm font-medium">Items</label>
      <div class="space-y-3">
        <!-- Editable items -->
        <div
          v-for="(item, index) in items"
          :key="index"
          class="flex items-center gap-3"
        >
          <input
            v-model="item.item_name"
            :disabled="!isEditing"
            placeholder="Item name"
            class="flex-1 rounded-md border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
            :required="isEditing"
          />
          <input
            v-model.number="item.quantity"
            type="number"
            min="1"
            :disabled="!isEditing"
            placeholder="Qty"
            class="w-20 rounded-md border border-gray-300 p-2 focus:border-transparent focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500"
            :required="isEditing"
          />
          <button
            v-if="isEditing"
            type="button"
            @click="removeItem(index)"
            class="rounded-md bg-red-100 px-3 py-2 text-red-700 transition-colors hover:bg-red-200"
            :class="{ 'cursor-not-allowed opacity-50': items.length <= 1 }"
            :disabled="items.length <= 1"
          >
            Remove
          </button>
        </div>

        <button
          v-if="isEditing"
          type="button"
          @click="addItem"
          class="mt-2 rounded-md bg-blue-100 px-4 py-2 text-blue-700 transition-colors hover:bg-blue-200"
        >
          + Add Another Item
        </button>

        <!-- Read-only display -->
        <div
          v-if="!isEditing"
          class="mt-4"
        >
          <div
            v-if="items.length > 0"
            class="space-y-2"
          >
            <div
              v-for="(item, index) in items"
              :key="index"
              class="flex justify-between border-b py-2"
            >
              <span class="font-medium">{{
                item.item_name || 'Unnamed item'
              }}</span>
              <span class="text-gray-600">Qty: {{ item.quantity }}</span>
            </div>
          </div>
          <div
            v-else
            class="py-2 italic text-gray-500"
          >
            No items added
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
