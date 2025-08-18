<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { useCorrections } from '@/composables/useCorrections'
import { getInitials } from '@/composables/useInitials'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import {
  JobOrderStatuses,
  type JobOrderStatus,
} from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import ArchiveColumn from '@/pages/job-orders/components/ArchiveColumn.vue'
import { Employee, JobOrder, SharedData, type BreadcrumbItem } from '@/types'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { compareDesc, format } from 'date-fns'
import { Calendar, LoaderCircle, Pencil, X } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import FifthSection from './components/sections/FifthSection.vue'
import FirstSection from './components/sections/FirstSection.vue'
import FourthSection from './components/sections/FourthSection.vue'
import SecondSection from './components/sections/SecondSection.vue'
import SixthSection from './components/sections/SixthSection.vue'
import ThirdSection from './components/sections/ThirdSection.vue'
import StatusUpdater from './components/StatusUpdater.vue'
import CorrectionRequestBanner from './components/CorrectionRequestBanner.vue'
import TicketHeader from '../components/TicketHeader.vue'

interface WasteManagementEditProps {
  data: {
    jobOrder: JobOrder
    employees?: Employee[]
  }
}

const props = defineProps<WasteManagementEditProps>()

const serviceDate = new Date(props.data.jobOrder.dateTime)

const { can } = usePermissions()
const { canUpdateProposalInformation } = useWasteManagementStages()

const canUpdateProposal = computed(() => {
  return canUpdateProposalInformation(props.data.jobOrder.status)
})

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
  payment_date: props.data.jobOrder.serviceable?.paymentDate,
  payment_type: props.data.jobOrder.serviceable?.form3?.paymentType,
  bid_bond: props.data.jobOrder.serviceable?.bidBond,
  or_number: props.data.jobOrder.serviceable?.orNumber,
  status: props.data.jobOrder.status,
  approved_date: props.data.jobOrder.serviceable?.form3?.approvedDate,
})

watch(
  () => props.data.jobOrder,
  (newValue) => {
    const { serviceable: service } = newValue
    const form3 = service?.form3
    const newDate = new Date(newValue.dateTime)

    form.date_time = newDate.toISOString()
    form.client = newValue.client
    form.address = newValue.address
    form.department = newValue.department
    form.contact_position = newValue.contactPosition
    form.contact_person = newValue.contactPerson
    form.contact_no = newValue.contactNo
    form.payment_date = service?.paymentDate
    form.payment_type = form3?.paymentType
    form.bid_bond = service?.bidBond
    form.or_number = service?.orNumber
    form.status = newValue.status
    form.approved_date = form3?.approvedDate
  },
)

const onSubmit = () => {
  form.patch(
    route(
      'job_order.waste_management.update',
      props.data.jobOrder.serviceable.id,
    ),
    {
      preserveScroll: true,
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message, {
          position: 'top-right',
        })
      },
    },
  )
}

const reason = ref<string>('')

const onSubmitCorrection = () => {
  const { canCorrectProposalInformation } = useCorrections()
  const [hours, min] = form.time.split(':')
  const epoch = new Date(form.date_time).setHours(Number(hours), Number(min))
  form
    .transform((data) => ({
      date_time: new Date(epoch).toLocaleString(),
      client: data.client,
      address: data.address,
      department: data.department,
      contact_position: data.contact_position,
      contact_person: data.contact_person,
      contact_no: data.contact_no,
      reason: reason.value,
      ...(canCorrectProposalInformation(props.data.jobOrder.status)
        ? {
            payment_date: new Date(data.payment_date).toLocaleString(),
            or_number: data.or_number,
            bid_bond: data.bid_bond,
            payment_type: data.payment_type,
            approved_date: new Date(data.approved_date).toLocaleString(),
          }
        : {}),
    }))
    .post(route('job_order.correction.store', props.data.jobOrder.ticket), {
      onSuccess: (page: any) => {
        form.reset()
        isEditing.value = false
        toast.success(page.props.flash.message, {
          position: 'top-right',
        })
      },
    })
}

const loadEmployees = () => router.reload({ only: ['employees'] })

const isEditing = ref<boolean>(false)

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
</script>

<template>
  <Head :title="data.jobOrder.ticket" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div
        :class="['sticky top-0 z-10 border-b border-border bg-background shadow-sm', {
          'top-[57px]': isNotHeadFrontliner
        }]"
      >
        <CorrectionRequestBanner :correction="unapprovedCorrections" />
        <div class="mb-3 flex items-center justify-between">
          <TicketHeader :job-order="data.jobOrder" />
          <div
            v-if="can('submit:job_order_correction')"
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
                  @click="() => (isEditing = !isEditing)"
                >
                  <Pencil class="mr-2" />
                  Request Correction
                </Button>
                <Button
                  v-show="isEditing"
                  variant="outline"
                  @click="() => (isEditing = !isEditing)"
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
              <div>
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
              </div>
              <div class="mt-2">
                <Separator class="mb-3 w-full" />
                <SecondSection
                  :status="data.jobOrder.status"
                  :dispatcher="data.jobOrder.serviceable?.dispatcher"
                  :appraisers="data.jobOrder.serviceable.appraisers"
                  :appraised-date="
                    data.jobOrder.serviceable?.form3?.appraisedDate
                  "
                  :serviceable-id="data.jobOrder.serviceable.id"
                  :employees="data.employees"
                  @load-employees="loadEmployees"
                />
              </div>
              <div class="mt-2">
                <Separator class="mb-3 w-full" />
                <ThirdSection
                  :is-editing="isEditing && canUpdateProposal"
                  :is-submit-btn-disabled="form.processing"
                  :status="data.jobOrder.status"
                  :errors="form.errors"
                  v-model:payment-type="form.payment_type"
                  v-model:bid-bond="form.bid_bond"
                  v-model:or-number="form.or_number"
                  v-model:payment-date="form.payment_date"
                  v-model:approved-date="form.approved_date"
                  :employees="data.employees"
                  @on-submit="onSubmit"
                  @on-cancel-submit="form.cancel()"
                />
              </div>
              <div class="mt-2">
                <Separator class="mb-3 w-full" />
                <FourthSection
                  :status="data.jobOrder.status"
                  :starting-date="data.jobOrder.serviceable?.form3?.from"
                  :ending-date="data.jobOrder.serviceable?.form3?.to"
                  :serviceable-id="data.jobOrder.serviceable.id"
                  :dispatcher="data.jobOrder.serviceable?.dispatcher"
                />
              </div>
              <div
                v-if="data.jobOrder.serviceable.form3?.haulings?.length"
                class="mt-2"
              >
                <Separator class="mb-3 w-full" />
                <FifthSection
                  :status="data.jobOrder.status"
                  :haulings="data.jobOrder.serviceable.form3?.haulings"
                  :employees="data.employees"
                  :serviceable-id="data.jobOrder.serviceable.id"
                  @load-employees="loadEmployees"
                />
              </div>
              <div v-if="isEditing">
                <Separator class="col-[1/-1] mb-3 w-full" />
                <SixthSection
                  ref="sixthSection"
                  :status="data.jobOrder.status"
                  :error="form.errors?.reason"
                  v-model:reason="reason"
                />
              </div>
              <div
                v-if="can('update:job_order') && isEditing"
                class="col-[1/-1] mt-4 flex w-full items-center"
              >
                <div class="ml-auto space-x-3">
                  <Button
                    v-show="form.processing"
                    type="button"
                    variant="outline"
                    @click="form.cancel()"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    variant="default"
                    :disabled="form.processing || !form.isDirty"
                    @click="onSubmitCorrection"
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
