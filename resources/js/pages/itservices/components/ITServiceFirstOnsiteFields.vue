<script setup lang="ts">
import { ref, computed } from 'vue'
import { MachineStatusOption } from '../types/types'
import { useForm } from '@inertiajs/vue3'

interface ValidationRule {
  required?: boolean;
  minLength?: number;
  maxLength?: number;
  fileTypes?: string[];
  maxFileSize?: number; // in MB
}

interface ValidationRules {
  [key: string]: ValidationRule;
}

interface ValidationErrors {
  [key: string]: string;
}

const props = defineProps<{
  form: ReturnType<typeof useForm<any>>;
  machineStatuses: MachineStatusOption[];
}>()

// Validation state
const showValidation = ref(false)

// Validation rules - UPDATED FIELD NAMES
const validationRules: ValidationRules = {
  first_service_performed: { // Changed from service_performed
    required: true,
    minLength: 10,
    maxLength: 1000
  },
  first_recommendation: { // Changed from recommendation
    required: true,
    minLength: 10,
    maxLength: 1000
  },
  first_machine_status: { // Changed from machine_status
    required: true
  },
  first_attached_file: { // Changed from attached_file
    required: false, // Optional field
    fileTypes: ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'],
    maxFileSize: 10 // 10MB
  }
}

// Validation functions
const validateField = (fieldName: string, value: any): string | null => {
  const rules = validationRules[fieldName]
  if (!rules) return null

  // Handle file validation separately
  if (fieldName === 'first_attached_file') {
    if (!value) return null // File is optional

    if (rules.maxFileSize && value.size > rules.maxFileSize * 1024 * 1024) {
      return `File size must not exceed ${rules.maxFileSize}MB`
    }

    if (rules.fileTypes) {
      const fileName = value.name.toLowerCase()
      const hasValidExtension = rules.fileTypes.some(ext => fileName.endsWith(ext.toLowerCase()))
      if (!hasValidExtension) {
        return `File must be one of: ${rules.fileTypes.join(', ')}`
      }
    }

    return null
  }

  if (rules.required && (!value || value.toString().trim() === '')) {
    return `${fieldName.replace('first_', '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} is required`
  }

  const stringValue = value ? value.toString().trim() : ''

  if (rules.minLength && stringValue.length < rules.minLength) {
    return `${fieldName.replace('first_', '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} must be at least ${rules.minLength} characters`
  }

  if (rules.maxLength && stringValue.length > rules.maxLength) {
    return `${fieldName.replace('first_', '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} must not exceed ${rules.maxLength} characters`
  }

  return null
}

// Computed validation errors
const errors = computed((): ValidationErrors => {
  if (!showValidation.value) return {}

  const validationErrors: ValidationErrors = {}
  Object.keys(validationRules).forEach(field => {
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
  return Object.keys(validationRules).every(field => {
    const error = validateField(field, props.form[field])
    // Only required fields need to be valid for form submission
    if (validationRules[field].required === false) {
      return true // Optional fields don't block submission
    }
    return !error
  })
})

// Validate form on submit
const validateForm = (): boolean => {
  showValidation.value = true
  return isValidForm.value
}

// Handle file change with validation
const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0] || null
  props.form.first_attached_file = file // Updated field name
}

// Expose validation functions
defineExpose({
  validateForm,
  isValidForm,
  errors,
  showValidation
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white px-6 py-4">
      <h2 class="text-lg font-medium text-gray-700">First Onsite Visit Details</h2>
    </div>

    <!-- Service Performed -->
    <div class="bg-white px-6 space-y-4">
      <div class="flex flex-col md:flex-row gap-3">
        <label class="w-40 text-sm font-medium text-gray-700 mt-2">Service Performed</label>
        <div class="flex flex-col flex-1">
          <textarea v-model="form.first_service_performed" :class="[
            'input-field flex-1',
            hasError('first_service_performed') ? 'border-red-500 bg-red-50' : ''
          ]" rows="4" placeholder="Describe the service performed in detail (minimum 10 characters)"></textarea>
          <span v-if="hasError('first_service_performed')" class="text-red-500 text-xs mt-1">
            {{ getError('first_service_performed') }}
          </span>
          <span class="text-gray-500 text-xs mt-1">
            {{ form.first_service_performed?.length || 0 }}/1000 characters
          </span>
        </div>
      </div>
    </div>

    <!-- Recommendation -->
    <div class="bg-white px-6 space-y-4">
      <div class="flex flex-col md:flex-row gap-3">
        <label class="w-40 text-sm font-medium text-gray-700 mt-2">Recommendation</label>
        <div class="flex flex-col flex-1">
          <textarea v-model="form.first_recommendation" :class="[
            'input-field flex-1',
            hasError('first_recommendation') ? 'border-red-500 bg-red-50' : ''
          ]" rows="4" placeholder="Provide technician's recommendation (minimum 10 characters)"></textarea>
          <span v-if="hasError('first_recommendation')" class="text-red-500 text-xs mt-1">
            {{ getError('first_recommendation') }}
          </span>
          <span class="text-gray-500 text-xs mt-1">
            {{ form.first_recommendation?.length || 0 }}/1000 characters
          </span>
        </div>
      </div>
    </div>

    <!-- Machine Status -->
    <div class="bg-white px-6 space-y-4">
      <div class="flex items-start gap-3">
        <label class="w-40 text-sm font-medium text-gray-700 mt-2">Machine Status</label>
        <div class="flex flex-col flex-1">
          <select v-model="form.first_machine_status" :class="[
            'input-field flex-1',
            hasError('first_machine_status') ? 'border-red-500 bg-red-50' : ''
          ]">
            <option disabled value="">Select current machine status</option>
            <option v-for="status in machineStatuses" :key="status.value" :value="status.value">
              {{ status.label }}
            </option>
          </select>
          <span v-if="hasError('first_machine_status')" class="text-red-500 text-xs mt-1">
            {{ getError('first_machine_status') }}
          </span>
        </div>
      </div>
    </div>

    <!-- Technician Report File -->
    <div class="bg-white px-6 space-y-4">
      <div class="flex flex-col md:flex-row gap-3">
        <label class="w-40 text-sm font-medium text-gray-700 mt-2">Attach Report</label>
        <div class="flex flex-col flex-1">
          <input type="file" @change="handleFileChange" :class="[
            'input-field flex-1',
            hasError('first_attached_file') ? 'border-red-500 bg-red-50' : ''
          ]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
          <span v-if="hasError('first_attached_file')" class="text-red-500 text-xs mt-1">
            {{ getError('first_attached_file') }}
          </span>
          <span class="text-gray-500 text-xs mt-1">
            Optional - Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)
          </span>
          <div v-if="form.first_attached_file" class="mt-2 p-2 bg-gray-50 rounded text-sm">
            <span class="text-green-600">✓</span> Selected: {{ form.first_attached_file.name }}
            ({{ Math.round(form.first_attached_file.size / 1024) }} KB)
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  @apply border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-blue-200 focus:border-blue-200 transition-colors;
}
</style>