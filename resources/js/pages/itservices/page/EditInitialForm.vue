<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import ITServiceFields from '../components/ITServiceFields.vue'
import type { Technician } from '../types/types'

interface FormComponentInstance {
  validateForm(): boolean
  isValidForm: boolean
  errors: { [key: string]: string }
  showValidation: any
}

interface Props {
  jobOrderId: number
  itServiceId: number
  technicians: Technician[]
  machineTypes: string[]
  jobOrderNumber?: string
  jobOrder: {
    client: string
    address: string
    department: string
    contact_no: string
    contact_person: string
    date_time?: string
  }
  itService: {
    technician_id: string
    machine_type: string
    model: string
    serial_no: string
    tag_no: string
    machine_problem: string
  }
}

const props = defineProps<Props>()

const formComponent = ref<FormComponentInstance | null>(null)

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
})

const submitForm = () => {
  if (formComponent.value?.validateForm) {
    const isValid = formComponent.value.validateForm()

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
  } else {
    return
  }

  const jobOrderId = String(props.jobOrderId)
  const itServiceId = String(props.itServiceId)

  router.post(
    route('job_order.it_service.corrections.store', {
      jobOrder: jobOrderId,
      itService: itServiceId,
    }),
    {
      fields: form.data(),
    },
    {
      onSuccess: () => {
        if (formComponent.value?.showValidation) {
          formComponent.value.showValidation.value = false
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
  <AppLayout title="Edit Initial IT Service">
    <div class="px-4 py-8 md:px-12 xl:px-20">
      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-700">
            Edit Initial IT Service
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Update the service request details
          </p>
        </div>
        <div
          v-if="jobOrderNumber"
          class="text-lg font-semibold text-blue-900"
        >
          Job Order: {{ jobOrderNumber }}
        </div>
      </div>

      <form
        @submit.prevent="submitForm"
        class="space-y-6"
      >
        <ITServiceFields
          ref="formComponent"
          :form="form"
          :technicians="props.technicians"
          :machineTypes="props.machineTypes"
        />

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
            <span v-else>Request Update</span>
          </button>
        </div>

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
            Service updated successfully!
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
