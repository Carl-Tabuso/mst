<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { usePermissions } from '@/composables/usePermissions'
import { type JobOrderStatus } from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import ArchiveColumn from '@/pages/job-orders/components/ArchiveJobOrder.vue'
import { Employee, JobOrder, SharedData, type BreadcrumbItem } from '@/types'
import {  useForm, usePage } from '@inertiajs/vue3'
import { LoaderCircle, Pencil, X,   } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import { toast } from 'vue-sonner'
import TicketHeader from '../components/TicketHeader.vue'
import CorrectionRequestBanner from '@/components/CorrectionRequestBanner.vue'
import FirstSection from '../waste-managements/components/sections/FirstSection.vue'
import Form5Section from '../other-services/components/Form5Section.vue'
import StatusUpdater from '../waste-managements/components/StatusUpdater.vue'

interface Form5EditProps {
  data: {
    jobOrder: JobOrder
  }
  employees?: Employee[]
}

const props = defineProps<Form5EditProps>()

const serviceDate = new Date(props.data.jobOrder.dateTime)

const { can } = usePermissions()
const employees = computed(() => props.employees || [])
const isEditing = ref<boolean>(false)
const reason = ref<string>('')

const form = useForm<Record<string, any>>({
  date_time: serviceDate.toISOString(),
  time: serviceDate.toLocaleTimeString(undefined, {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  }),
  client: props.data.jobOrder.client,
  address: props.data.jobOrder.address,
  department: props.data.jobOrder.department,
  contact_position: props.data.jobOrder.contactPosition,
  contact_person: props.data.jobOrder.contactPerson,
  contact_no: props.data.jobOrder.contactNo,
  assigned_person: props.data.jobOrder.serviceable?.assigned_person || null,
  purpose: props.data.jobOrder.serviceable?.purpose || '',
  items: props.data.jobOrder.serviceable?.items || [],
})

const assignedPersonId = computed(() => {
  const assignedPerson = props.data.jobOrder.serviceable?.assigned_person
  if (!assignedPerson) return null
  
  return typeof assignedPerson === 'string' 
    ? parseInt(assignedPerson, 10) 
    : assignedPerson
})

const resetFormToOriginal = () => {
  form.date_time = serviceDate.toISOString()
  form.time = serviceDate.toLocaleTimeString(undefined, {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  })
  form.client = props.data.jobOrder.client
  form.address = props.data.jobOrder.address
  form.department = props.data.jobOrder.department
  form.contact_position = props.data.jobOrder.contactPosition
  form.contact_person = props.data.jobOrder.contactPerson
  form.contact_no = props.data.jobOrder.contactNo
  form.assigned_person = assignedPersonId.value
  form.purpose = props.data.jobOrder.serviceable?.purpose || ''
  form.items = props.data.jobOrder.serviceable?.items || []
  reason.value = ''
}

const onSubmitCorrection = () => {
  const [hours, min] = form.time.split(':')
  const epoch = new Date(form.date_time).setHours(Number(hours), Number(min))
  
  const submissionData = {
    date_time: new Date(epoch).toLocaleString(),
    client: form.client,
    address: form.address,
    department: form.department,
    contact_position: form.contact_position,
    contact_person: form.contact_person,
    contact_no: form.contact_no,
    assigned_person: form.assigned_person,
    purpose: form.purpose,
    items: form.items,
    reason: reason.value,
  };
  
  form
    .transform(() => submissionData)
    .post(route('job_order.correction.store', props.data.jobOrder.ticket), {
      onSuccess: (page: any) => {
        isEditing.value = false
        resetFormToOriginal()
        toast.success(page.props.flash.message, {
          position: 'top-right',
        })
      },
      onError: (errors) => {
        toast.error('Please check the form for errors', {
          position: 'top-right',
        })
      },
    })
}

const manualStatuses: Array<JobOrderStatus> = [
  'for viewing',
  'for proposal',
  'in-progress',
]

const canManuallyUpdate = computed(() =>
  manualStatuses.includes(props.data.jobOrder.status),
)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders',
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: props.data.jobOrder.ticket,
    href: '#',
  },
]

const page = usePage<SharedData>()

onMounted(() => {
  const message = page.props.flash.message

  if (message) {
    toast.success(message.title, {
      description: message.description,
      position: 'top-center',
    })
  }
})

const unapprovedCorrections = computed(() => {
  return props.data.jobOrder.corrections?.find(
    (correction) => !correction.approvedAt,
  )
})

const isNotHeadFrontliner = computed(() => {
  return page.props.auth.user.roles[0].name !== 'head frontliner'
})

const toggleEditMode = () => {
  isEditing.value = !isEditing.value
  if (!isEditing.value) {
    resetFormToOriginal()
  }
}
</script>

<template>
  <Head :title="data.jobOrder.ticket" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div
        :class="[
          'sticky top-0 z-10 border-b border-border bg-background shadow-sm',
          {
            'top-[57px]': isNotHeadFrontliner,
          },
        ]"
      >
        <CorrectionRequestBanner :correction="unapprovedCorrections" />
        <div class="mb-3 flex items-center justify-between">
          <TicketHeader :job-order="data.jobOrder" />
          <div
            v-if="can('create:job_order_correction')"
            class="flex h-8 items-center gap-2"
          >
            <div
              v-if="canManuallyUpdate"
              class="ml-auto"
            >
              <StatusUpdater
                :status="data.jobOrder.status"
                :ticket="data.jobOrder.ticket"
              />
            </div>
            <Separator
              v-if="canManuallyUpdate"
              orientation="vertical"
            />
            <div class="flex gap-5">
              <div
                v-if="!unapprovedCorrections"
                class="flex gap-5"
              >
                <Button
                  v-show="!isEditing"
                  variant="outline"
                  @click="toggleEditMode"
                >
                  <Pencil class="mr-2" />
                  Request Correction
                </Button>
                <Button
                  v-show="isEditing"
                  variant="outline"
                  @click="toggleEditMode"
                >
                  <X class="mr-2" />
                  Cancel Correction
                </Button>
              </div>
              <ArchiveColumn :jobOrder="data.jobOrder" />
            </div>
          </div>
        </div>
      </div>
      
      <div class="my-4 flex flex-col gap-4 rounded-xl">
        <div class="mb-3 flex items-center">
          <div class="flex w-full flex-col">
            <form class="grid gap-y-6">
              <!-- First Section (Basic Info) -->
              <FirstSection
                :is-editing="isEditing && can('update:job_order')"
                :is-service-type-disabled="true"
                v-model:service-type="data.jobOrder.serviceableType"
                v-model:service-date="form.date_time"
                v-model:service-time="form.time"
                v-model:client="form.client"
                v-model:address="form.address"
                v-model:department="form.department"
                v-model:contact-position="form.contact_position"
                v-model:contact-person="form.contact_person"
                v-model:contact-number="form.contact_no"
              />
              
              <!-- Form5 Section -->
              <div v-if="data.jobOrder.serviceable">
                <Separator class="mb-3 w-full" />
                <Form5Section
                  :is-editing="isEditing && can('update:job_order')"
                  v-model:assigned-person="form.assigned_person"
                  v-model:items="form.items"
                  v-model:purpose="form.purpose"
                  :employees="employees"
                  :initial-assigned-person="assignedPersonId"
                />
              </div>
              
              <!-- Correction Reason (only shown when editing) -->
              <div v-if="isEditing">
                <Separator class="col-[1/-1] mb-3 w-full" />
                <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3 items-center">
                  <label class="text-sm font-medium">Correction Reason</label>
                  <div class="w-full">
                    <textarea
                      v-model="reason"
                      placeholder="Please explain why this correction is needed"
                      class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      rows="3"
                      required
                    />
                    <p v-if="form.errors?.reason" class="text-red-500 text-xs mt-1">
                      {{ form.errors.reason }}
                    </p>
                  </div>
                </div>
              </div>
              
              <!-- Submit Button (only shown when editing) -->
              <div
                v-if="isEditing && can('update:job_order')"
                class="col-[1/-1] mt-4 flex w-full items-center"
              >
                <div class="ml-auto space-x-3">
                  <Button
                    type="button"
                    variant="outline"
                    @click="toggleEditMode"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    variant="default"
                    :disabled="form.processing || !form.isDirty || !reason"
                    @click.prevent="onSubmitCorrection"
                  >
                    <LoaderCircle
                      v-show="form.processing"
                      class="animate-spin"
                    />
                    Submit Corrections
                  </Button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>