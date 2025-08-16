<script setup lang="ts">
import { ref, computed } from 'vue'
import { MachineStatusOption } from '../types/types'
import { useForm } from '@inertiajs/vue3'

interface ValidationRule {
    required?: boolean;
    minLength?: number;
    maxLength?: number;
    fileTypes?: string[];
    maxFileSize?: number;
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

const showValidation = ref(false)

const validationRules: ValidationRules = {
    first_service_performed: {
        required: true,
        minLength: 10,
        maxLength: 1000
    },
    recommendation: {
        required: true,
        minLength: 10,
        maxLength: 1000
    },
    machine_status: {
        required: true
    },
    attached_file: {
        required: false,
        fileTypes: ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'],
        maxFileSize: 10
    }
}

const validateField = (fieldName: string, value: any): string | null => {
    const rules = validationRules[fieldName]
    if (!rules) return null

    if (fieldName === 'attached_file') {
        if (!value) return null

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
        return `${fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} is required`
    }

    const stringValue = value ? value.toString().trim() : ''

    if (rules.minLength && stringValue.length < rules.minLength) {
        return `${fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} must be at least ${rules.minLength} characters`
    }

    if (rules.maxLength && stringValue.length > rules.maxLength) {
        return `${fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} must not exceed ${rules.maxLength} characters`
    }

    return null
}

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

const hasError = (fieldName: string): boolean => {
    return showValidation.value && !!errors.value[fieldName]
}

const getError = (fieldName: string): string => {
    return errors.value[fieldName] || ''
}

const isValidForm = computed((): boolean => {
    return Object.keys(validationRules).every(field => {
        const error = validateField(field, props.form[field])
        if (validationRules[field].required === false) {
            return true
        }
        return !error
    })
})

const validateForm = (): boolean => {
    showValidation.value = true
    return isValidForm.value
}

// Handle file change with validation
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0] || null
    props.form.attached_file = file
}

defineExpose({
    validateForm,
    isValidForm,
    errors,
    showValidation
})
</script>

<template>
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-4  border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300">First Onsite Visit Details</h2>
        </div>

        <!-- Service Performed -->
        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-4  border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="w-full sm:w-40 text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Service
                    Performed</label>
                <div class="flex flex-col flex-1">
                    <textarea v-model="form.first_service_performed" :class="[
                        'input-field flex-1',
                        hasError('first_service_performed') ? 'border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-400' : ''
                    ]" rows="4" placeholder="Describe the service performed in detail (minimum 10 characters)"></textarea>
                    <span v-if="hasError('first_service_performed')"
                        class="text-red-500 dark:text-red-400 text-xs mt-1">
                        {{ getError('first_service_performed') }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        {{ form.first_service_performed?.length || 0 }}/1000 characters
                    </span>
                </div>
            </div>
        </div>

        <!-- Recommendation -->
        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-4  border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row gap-3">
                <label
                    class="w-full sm:w-40 text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Recommendation</label>
                <div class="flex flex-col flex-1">
                    <textarea v-model="form.recommendation" :class="[
                        'input-field flex-1',
                        hasError('recommendation') ? 'border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-400' : ''
                    ]" rows="4" placeholder="Provide technician's recommendation (minimum 10 characters)"></textarea>
                    <span v-if="hasError('recommendation')" class="text-red-500 dark:text-red-400 text-xs mt-1">
                        {{ getError('recommendation') }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        {{ form.recommendation?.length || 0 }}/1000 characters
                    </span>
                </div>
            </div>
        </div>

        <!-- Machine Status -->
        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-4  border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                <label class="w-full sm:w-40 text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Machine
                    Status</label>
                <div class="flex flex-col flex-1">
                    <select v-model="form.machine_status" :class="[
                        'input-field flex-1',
                        hasError('machine_status') ? 'border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-400' : ''
                    ]">
                        <option disabled value="">Select current machine status</option>
                        <option v-for="status in machineStatuses" :key="status.value" :value="status.value">
                            {{ status.label }}
                        </option>
                    </select>
                    <span v-if="hasError('machine_status')" class="text-red-500 dark:text-red-400 text-xs mt-1">
                        {{ getError('machine_status') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Technician Report File -->
        <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-4  border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="w-full sm:w-40 text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Attach
                    Report</label>
                <div class="flex flex-col flex-1">
                    <input type="file" @change="handleFileChange" :class="[
                        'input-field flex-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/50 dark:file:text-blue-300 dark:hover:file:bg-blue-900/70',
                        hasError('attached_file') ? 'border-red-500 bg-red-50 dark:bg-red-900/20 dark:border-red-400' : ''
                    ]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                    <span v-if="hasError('attached_file')" class="text-red-500 dark:text-red-400 text-xs mt-1">
                        {{ getError('attached_file') }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        Optional - Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)
                    </span>
                    <div v-if="form.attached_file"
                        class="mt-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded text-sm border border-gray-200 dark:border-gray-600">
                        <span class="text-green-600 dark:text-green-400">✓</span>
                        <span class="text-gray-700 dark:text-gray-300">Selected: {{ form.attached_file.name }}</span>
                        <span class="text-gray-500 dark:text-gray-400">({{ Math.round(form.attached_file.size / 1024) }}
                            KB)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.input-field {
    @apply border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-200 dark:focus:ring-blue-600 focus:border-blue-200 dark:focus:border-blue-600 transition-colors;
}
</style>