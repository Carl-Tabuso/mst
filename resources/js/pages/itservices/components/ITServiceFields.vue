<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Technician } from '../types/types'

interface ValidationRule {
  required?: boolean
  minLength?: number
  maxLength?: number
  pattern?: RegExp
  futureDate?: boolean
}

interface ValidationRules {
  [key: string]: ValidationRule
}

interface ValidationErrors {
  [key: string]: string
}

const props = defineProps<{
  form: ReturnType<typeof useForm<any>>
  technicians: Technician[]
  machineTypes: string[]
}>()

// Validation state
const showValidation = ref(false)

// Validation rules
const validationRules: ValidationRules = {
  client: {
    required: true,
    minLength: 2,
    maxLength: 100,
    pattern: /^[a-zA-Z0-9\s.,'-]+$/,
  },
  address: {
    required: true,
    minLength: 10,
    maxLength: 255,
  },
  department: {
    required: true,
    minLength: 2,
    maxLength: 50,
  },
  contact_person: {
    required: true,
    minLength: 2,
    maxLength: 50,
    pattern: /^[a-zA-Z\s.,'-]+$/,
  },
  contact_no: {
    required: true,
    pattern: /^(09|\+639)\d{9}$/,
  },
  date: {
    required: true,
    futureDate: true,
  },
  time: {
    required: true,
  },
  technician_id: {
    required: true,
  },
  machine_type: {
    required: true,
  },
  model: {
    required: true,
    minLength: 2,
    maxLength: 50,
  },
  serial_no: {
    required: true,
    minLength: 3,
    maxLength: 50,
    pattern: /^[a-zA-Z0-9-]+$/,
  },
  tag_no: {
    required: true,
    minLength: 2,
    maxLength: 20,
    pattern: /^[a-zA-Z0-9-]+$/,
  },
  machine_problem: {
    required: true,
    minLength: 10,
    maxLength: 500,
  },
}

// Validation functions
const validateField = (fieldName: string, value: any): string | null => {
  const rules = validationRules[fieldName]
  if (!rules) return null

  if (rules.required && (!value || value.toString().trim() === '')) {
    return `${fieldName.replace('_', ' ')} is required`
  }

  const stringValue = value ? value.toString().trim() : ''

  if (rules.minLength && stringValue.length < rules.minLength) {
    return `${fieldName.replace('_', ' ')} must be at least ${rules.minLength} characters`
  }

  if (rules.maxLength && stringValue.length > rules.maxLength) {
    return `${fieldName.replace('_', ' ')} must not exceed ${rules.maxLength} characters`
  }

  if (rules.pattern && stringValue && !rules.pattern.test(stringValue)) {
    switch (fieldName) {
      case 'contact_no':
        return 'Contact number must be a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)'
      case 'client':
        return 'Client name contains invalid characters'
      case 'contact_person':
        return 'Contact person name contains invalid characters'
      case 'serial_no':
        return 'Serial number can only contain letters, numbers, and hyphens'
      case 'tag_no':
        return 'Tag number can only contain letters, numbers, and hyphens'
      default:
        return `${fieldName.replace('_', ' ')} format is invalid`
    }
  }

  if (rules.futureDate && fieldName === 'date') {
    const selectedDate = new Date(stringValue)
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    const minDate = new Date(today)
    minDate.setFullYear(minDate.getFullYear() - 5)

    const maxDate = new Date(today)
    maxDate.setFullYear(maxDate.getFullYear() + 5)

    if (selectedDate < minDate || selectedDate > maxDate) {
      return `Date must be within ${minDate.getFullYear()} and ${maxDate.getFullYear()}`
    }
  }

  return null
}

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
    return !validateField(field, props.form[field])
  })
})

const validateForm = (): boolean => {
  showValidation.value = true
  return isValidForm.value
}

defineExpose({
  validateForm,
  isValidForm,
  errors,
  showValidation,
})
</script>

<template>
  <div class="space-y-4 sm:space-y-6">
    <!-- Type of Service -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-0">
        <!-- Label -->
        <label
          class="w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-44"
          >Type of Service</label
        >

        <!-- Radio buttons -->
        <div class="flex flex-wrap gap-6">
          <label class="flex items-center">
            <input
              type="radio"
              v-model="form.service_type"
              value="waste_management"
              class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
              disabled
            />
            <span class="ml-2 text-sm text-gray-400 dark:text-gray-500"
              >Waste Management</span
            >
          </label>

          <label class="flex items-center">
            <input
              type="radio"
              v-model="form.service_type"
              value="it_services"
              class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
              checked
              disabled
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300"
              >IT Services</span
            >
          </label>

          <label class="flex items-center">
            <input
              type="radio"
              v-model="form.service_type"
              value="other_services"
              class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
              disabled
            />
            <span class="ml-2 text-sm text-gray-400 dark:text-gray-500"
              >Other Services (specify)</span
            >
          </label>
        </div>
      </div>
    </div>

    <!-- Date and Time of Service -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
        <label
          class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
          >Date and Time of Service</label
        >
        <div class="flex flex-col gap-2">
          <div class="flex gap-3">
            <div class="flex flex-col">
              <input
                type="date"
                v-model="form.date"
                :class="[
                  'input-field',
                  hasError('date')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
              />
              <span
                v-if="hasError('date')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('date') }}
              </span>
            </div>
            <div class="flex flex-1 flex-col">
              <input
                type="time"
                v-model="form.time"
                :class="[
                  'input-field',
                  hasError('time')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
              />
              <span
                v-if="hasError('time')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('time') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Information -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">
          Client's Info
        </h3>

        <!-- Client -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
          <label
            class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
            >Client</label
          >
          <div class="flex flex-1 flex-col">
            <input
              v-model="form.client"
              :class="[
                'input-field flex-1',
                hasError('client')
                  ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                  : '',
              ]"
              placeholder="Enter client/company name"
            />
            <span
              v-if="hasError('client')"
              class="mt-1 text-xs text-red-500 dark:text-red-400"
            >
              {{ getError('client') }}
            </span>
          </div>
        </div>

        <!-- Address -->
        <div class="flex flex-col gap-3 sm:flex-row">
          <label
            class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
            >Address</label
          >
          <div class="flex flex-1 flex-col">
            <textarea
              v-model="form.address"
              :class="[
                'input-field flex-1',
                hasError('address')
                  ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                  : '',
              ]"
              rows="3"
              placeholder="Enter client's complete address"
            ></textarea>
            <span
              v-if="hasError('address')"
              class="mt-1 text-xs text-red-500 dark:text-red-400"
            >
              {{ getError('address') }}
            </span>
          </div>
        </div>

        <!-- Contact Person & Contact Number -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Contact Person</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.contact_person"
                :class="[
                  'input-field flex-1',
                  hasError('contact_person')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="Enter contact person"
              />
              <span
                v-if="hasError('contact_person')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('contact_person') }}
              </span>
            </div>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Contact Number</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.contact_no"
                :class="[
                  'input-field flex-1',
                  hasError('contact_no')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="09XXXXXXXXX"
              />
              <span
                v-if="hasError('contact_no')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('contact_no') }}
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Department/Branch</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.department"
                :class="[
                  'input-field flex-1',
                  hasError('department')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="Enter client/company's department"
              />
              <span
                v-if="hasError('department')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('department') }}
              </span>
            </div>
          </div>
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Technician</label
            >
            <div class="flex flex-1 flex-col">
              <select
                v-model="form.technician_id"
                :class="[
                  'input-field flex-1',
                  hasError('technician_id')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
              >
                <option
                  disabled
                  value=""
                >
                  Select technician
                </option>
                <option
                  v-for="tech in technicians"
                  :key="tech.id"
                  :value="tech.id"
                >
                  {{ tech.first_name }} {{ tech.middle_name }}
                  {{ tech.last_name }} {{ tech.suffix }}
                </option>
              </select>
              <span
                v-if="hasError('technician_id')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('technician_id') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Machine Details -->
    <div
      class="border-gray-200 bg-white px-4 py-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6"
    >
      <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">
          Machine Details
        </h3>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <!-- Machine Type -->
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Machine Type</label
            >
            <div class="flex flex-1 flex-col">
              <select
                v-model="form.machine_type"
                :class="[
                  'input-field flex-1',
                  hasError('machine_type')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
              >
                <option
                  disabled
                  value=""
                >
                  Select machine type
                </option>
                <option
                  v-for="type in machineTypes"
                  :key="type"
                  :value="type"
                >
                  {{ type }}
                </option>
              </select>
              <span
                v-if="hasError('machine_type')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('machine_type') }}
              </span>
            </div>
          </div>

          <!-- Model -->
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Model</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.model"
                :class="[
                  'input-field flex-1',
                  hasError('model')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="Enter model"
              />
              <span
                v-if="hasError('model')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('model') }}
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <!-- Serial No -->
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Serial No</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.serial_no"
                :class="[
                  'input-field flex-1',
                  hasError('serial_no')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="Enter serial number"
              />
              <span
                v-if="hasError('serial_no')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('serial_no') }}
              </span>
            </div>
          </div>

          <!-- Tag No -->
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <label
              class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
              >Tag No</label
            >
            <div class="flex flex-1 flex-col">
              <input
                v-model="form.tag_no"
                :class="[
                  'input-field flex-1',
                  hasError('tag_no')
                    ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                    : '',
                ]"
                placeholder="Enter tag number"
              />
              <span
                v-if="hasError('tag_no')"
                class="mt-1 text-xs text-red-500 dark:text-red-400"
              >
                {{ getError('tag_no') }}
              </span>
            </div>
          </div>
        </div>

        <!-- Machine Problem -->
        <div class="flex flex-col gap-3 sm:flex-row">
          <label
            class="mt-2 w-full text-sm font-medium text-gray-700 dark:text-gray-300 sm:w-40"
            >Machine Problem</label
          >
          <div class="flex flex-1 flex-col">
            <textarea
              v-model="form.machine_problem"
              :class="[
                'input-field flex-1',
                hasError('machine_problem')
                  ? 'border-red-500 bg-red-50 dark:border-red-400 dark:bg-red-900/20'
                  : '',
              ]"
              rows="3"
              placeholder="Describe the machine problem (minimum 10 characters)"
            ></textarea>
            <span
              v-if="hasError('machine_problem')"
              class="mt-1 text-xs text-red-500 dark:text-red-400"
            >
              {{ getError('machine_problem') }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  @apply rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-500 transition-colors focus:border-blue-200 focus:outline-none focus:ring-1 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-blue-600 dark:focus:ring-blue-600;
}

@media (max-width: 475px) {
  .xs\:flex-row {
    flex-direction: row;
  }
}
</style>
