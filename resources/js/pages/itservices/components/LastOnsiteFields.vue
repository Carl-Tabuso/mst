<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { MachineStatusOption } from '../types/types'

interface ValidationRule {
  required?: boolean
  minLength?: number
  maxLength?: number
}

interface ValidationRules {
  [key: string]: ValidationRule
}

interface ValidationErrors {
  [key: string]: string
}

const props = defineProps<{
  form: ReturnType<typeof useForm<any>>
  machineStatuses: MachineStatusOption[]
}>()

// Validation state
const showValidation = ref(false)

// Validation rules
const validationRules: ValidationRules = {
  service_performed: {
    required: true,
    minLength: 10,
    maxLength: 1000,
  },
  parts_replaced: {
    required: true,
    minLength: 5,
    maxLength: 500,
  },
  final_remark: {
    required: true,
    minLength: 10,
    maxLength: 1000,
  },
  final_machine_status: {
    required: true,
  },
}

// Validation functions
const validateField = (fieldName: string, value: any): string | null => {
  const rules = validationRules[fieldName]
  if (!rules) return null

  if (rules.required && (!value || value.toString().trim() === '')) {
    return `${fieldName.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())} is required`
  }

  const stringValue = value ? value.toString().trim() : ''

  if (rules.minLength && stringValue.length < rules.minLength) {
    return `${fieldName.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())} must be at least ${rules.minLength} characters`
  }

  if (rules.maxLength && stringValue.length > rules.maxLength) {
    return `${fieldName.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())} must not exceed ${rules.maxLength} characters`
  }

  return null
}

// Computed validation errors
const errors = computed((): ValidationErrors => {
  if (!showValidation.value) return {}

  const validationErrors: ValidationErrors = {}
  Object.keys(validationRules).forEach((field) => {
    const error = validateField(field, props.form[field])
    if (error) {
      validationErrors[field] = error
    }
  })

  return validationErrors
})

// Check if field has error
const hasError = (fieldName: string): boolean => {
  return showValidation.value && !!errors.value[fieldName]
}

// Get field error message
const getError = (fieldName: string): string => {
  return errors.value[fieldName] || ''
}

// Form validation
const isValidForm = computed((): boolean => {
  return Object.keys(validationRules).every((field) => {
    const error = validateField(field, props.form[field])
    return !error
  })
})

// Validate form on submit
const validateForm = (): boolean => {
  showValidation.value = true
  return isValidForm.value
}

// Expose validation functions
defineExpose({
  validateForm,
  isValidForm,
  errors,
  showValidation,
})
</script>

<template>
  <div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">
        Final Onsite Visit Details
      </h2>
    </div>

    <!-- Service Performed -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row">
        <label
          class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
          >Service Performed</label
        >
        <div class="flex flex-1 flex-col">
          <textarea
            v-model="form.service_performed"
            :class="[
              'input-field flex-1',
              hasError('service_performed')
                ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                : '',
            ]"
            rows="4"
            placeholder="Describe all services performed during this final visit (minimum 10 characters)"
          ></textarea>
          <span
            v-if="hasError('service_performed')"
            class="mt-1 text-xs text-red-500 dark:text-red-400"
          >
            {{ getError('service_performed') }}
          </span>
          <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ form.service_performed?.length || 0 }}/1000 characters
          </span>
        </div>
      </div>
    </div>

    <!-- Parts Replaced -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row">
        <label
          class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
          >Parts Replaced</label
        >
        <div class="flex flex-1 flex-col">
          <textarea
            v-model="form.parts_replaced"
            :class="[
              'input-field flex-1',
              hasError('parts_replaced')
                ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                : '',
            ]"
            rows="3"
            placeholder="List all parts that were replaced or installed (minimum 5 characters)"
          ></textarea>
          <span
            v-if="hasError('parts_replaced')"
            class="mt-1 text-xs text-red-500 dark:text-red-400"
          >
            {{ getError('parts_replaced') }}
          </span>
          <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ form.parts_replaced?.length || 0 }}/500 characters
          </span>
        </div>
      </div>
    </div>

    <!-- Final Remark -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row">
        <label
          class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
          >Final Remark</label
        >
        <div class="flex flex-1 flex-col">
          <textarea
            v-model="form.final_remark"
            :class="[
              'input-field flex-1',
              hasError('final_remark')
                ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                : '',
            ]"
            rows="4"
            placeholder="Provide final remarks, current machine status, and any follow-up recommendations (minimum 10 characters)"
          ></textarea>
          <span
            v-if="hasError('final_remark')"
            class="mt-1 text-xs text-red-500 dark:text-red-400"
          >
            {{ getError('final_remark') }}
          </span>
          <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ form.final_remark?.length || 0 }}/1000 characters
          </span>
        </div>
      </div>
    </div>

    <!-- Machine Status -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row">
        <label
          class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
          >Machine Status</label
        >
        <div class="flex flex-1 flex-col">
          <select
            v-model="form.final_machine_status"
            :class="[
              'input-field',
              hasError('final_machine_status')
                ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                : '',
            ]"
          >
            <option value="">Select status</option>
            <option
              v-for="status in machineStatuses"
              :key="status.value"
              :value="status.value"
            >
              {{ status.label }}
            </option>
          </select>
          <span
            v-if="hasError('final_machine_status')"
            class="mt-1 text-xs text-red-500 dark:text-red-400"
          >
            {{ getError('final_machine_status') }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  @apply rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 transition-colors focus:border-blue-200 focus:outline-none focus:ring-1 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600;
}
</style>
