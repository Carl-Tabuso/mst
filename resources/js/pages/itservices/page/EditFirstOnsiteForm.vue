<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import ITServiceFields from '../components/ITServiceFields.vue'
import ITServiceFirstOnsiteFields from '../components/ITServiceFirstOnsiteFields.vue'
import type { MachineStatusOption, Technician } from '../types/types'

// Define the component instance type
interface FormComponentInstance {
  validateForm(): boolean
  isValidForm: boolean
  errors: { [key: string]: string }
  showValidation: any
}

interface Props {
  jobOrderId: number
  jobOrderNumber: string
  serviceId: number
  reportId: number
  technicians: Technician[]
  machineTypes: string[]
  machineStatuses: MachineStatusOption[]
  jobOrder: {
    id: number
    client: string
    address: string
    department: string
    contact_no: string
    contact_person: string
    date_time?: string
    job_order_no: string
  }
  itService: {
    id: number
    technician_id: string
    machine_type: string
    model: string
    serial_no: string
    tag_no: string
    machine_problem: string
    status: string
  }
  existingReport: {
    id: number
    service_performed: string
    recommendation: string
    machine_status: string
    attached_file?: string
  }
  isEdit: boolean
}

const props = defineProps<Props>()

const initialFormComponent = ref<FormComponentInstance | null>(null)
const firstOnsiteFormComponent = ref<FormComponentInstance | null>(null)

const dateTime = props.jobOrder.date_time
  ? new Date(props.jobOrder.date_time)
  : null

const form = useForm({
  client: props.jobOrder.client,
  address: props.jobOrder.address,
  department: props.jobOrder.department,
  contact_no: props.jobOrder.contact_no,
  contact_person: props.jobOrder.contact_person,
  date: dateTime ? dateTime.toISOString().split('T')[0] : '',
  time: dateTime ? dateTime.toTimeString().slice(0, 5) : '',
  technician_id: props.itService.technician_id,
  machine_type: props.itService.machine_type,
  model: props.itService.model,
  serial_no: props.itService.serial_no,
  tag_no: props.itService.tag_no,
  machine_problem: props.itService.machine_problem,

  service_performed: props.existingReport.service_performed,
  recommendation: props.existingReport.recommendation,
  machine_status: props.existingReport.machine_status,
  attached_file: null as File | null,
})

const submitForm = () => {
  let isValidInitial = true
  let isValidFirstOnsite = true

  const isValid = isValidInitial && isValidFirstOnsite

  if (!isValid) {
    setTimeout(() => {
      const firstError = document.querySelector(
        '.border-red-500',
      ) as HTMLElement
      if (firstError) {
        firstError.scrollIntoView({
          behavior: 'smooth',
          block: 'center',
        })
        if (['INPUT', 'SELECT', 'TEXTAREA'].includes(firstError.tagName)) {
          firstError.focus()
        }
      }
    }, 100)
    return
  }

  const jobOrderId = String(props.jobOrderId)
  const formData = form.data()

  if (!form.attached_file) {
    delete formData.attached_file
  }

  router.post(
    route('job_order.it_service.corrections.store', {
      jobOrder: jobOrderId,
    }),
    {
      fields: formData,
    },
    {
      forceFormData: true,
      onSuccess: () => {
        if (initialFormComponent.value?.showValidation) {
          initialFormComponent.value.showValidation.value = false
        }
        if (firstOnsiteFormComponent.value?.showValidation) {
          firstOnsiteFormComponent.value.showValidation.value = false
        }
      },
    },
  )
}

function goBack() {
  window.history.back()
}
</script>

<template>
  <AppLayout title="Edit First Onsite IT Service">
    <div class="px-4 py-8 md:px-12 xl:px-20">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-700">
            Edit First Onsite Visit
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Update the first onsite service report
          </p>
        </div>
        <div class="text-lg font-semibold text-blue-900">
          Job Order: {{ props.jobOrderId }}
        </div>
      </div>

      <form
        @submit.prevent="submitForm"
        class="space-y-6"
      >
        <!-- Initial Stage Fields -->
        <div class="p-6">
          <h2 class="mb-4 text-lg font-medium text-gray-700">
            Initial Information
          </h2>
          <ITServiceFields
            ref="initialFormComponent"
            :form="form"
            :technicians="props.technicians"
            :machineTypes="props.machineTypes"
          />
        </div>

        <!-- First Onsite Fields -->
        <div class="px-6">
          <ITServiceFirstOnsiteFields
            ref="firstOnsiteFormComponent"
            :form="form"
            :machineStatuses="props.machineStatuses"
          />
        </div>

        <!-- Current Attached File Display -->
        <div
          v-if="props.existingReport.attached_file"
          class="rounded-xl bg-blue-50 p-4"
        >
          <h3 class="mb-2 text-sm font-medium text-blue-800">
            Current Attached File
          </h3>
          <div class="flex items-center space-x-2">
            <svg
              class="h-4 w-4 text-blue-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
              />
            </svg>
            <a
              :href="
                route('job_order.it_service.report.download', {
                  jobOrder: jobOrder.id,
                  reportId: existingReport.id,
                })
              "
              target="_blank"
              class="text-sm text-blue-600 underline hover:text-blue-800"
            >
              View Current File
            </a>
          </div>
          <p class="mt-1 text-xs text-blue-600">
            Upload a new file above to replace the current one
          </p>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-8 flex justify-end gap-4 px-6">
          <button
            type="button"
            @click="goBack"
            class="rounded-xl bg-gray-200 px-6 py-2 text-gray-700 shadow-md transition hover:bg-gray-300"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="rounded-xl bg-blue-900 px-6 py-2 text-white shadow-md transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            <span
              v-if="form.processing"
              class="flex items-center"
            >
              <svg
                class="-ml-1 mr-2 h-4 w-4 animate-spin text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Updating...
            </span>
            <span v-else>Request Update </span>
          </button>
        </div>

        <!-- Success Message -->
        <div
          v-if="form.recentlySuccessful"
          class="text-center"
        >
          <div
            class="inline-flex items-center rounded-lg border border-green-200 bg-green-100 px-4 py-2 text-green-700"
          >
            <svg
              class="mr-2 h-4 w-4"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              ></path>
            </svg>
            First onsite report updated successfully!
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
