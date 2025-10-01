<script setup lang="ts">
import CorrectionRequestBanner from '@/components/CorrectionRequestBanner.vue'
import MainContainer from '@/components/MainContainer.vue'
import StickyPageHeader from '@/components/StickyPageHeader.vue'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { useCorrections } from '@/composables/useCorrections'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { type JobOrderStatus } from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import ArchiveJobOrder from '@/pages/job-orders/components/ArchiveJobOrder.vue'
import { BreadcrumbItem, Form4, JobOrder, Truck } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import { computed, provide, readonly, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import { GroupedEmployeesByAccountRole } from '.'
import RequestCorrectionButton from '../components/RequestCorrectionButton.vue'
import TicketHeader from '../components/TicketHeader.vue'
import FifthSection from './components/sections/FifthSection.vue'
import FirstSection from './components/sections/FirstSection.vue'
import FourthSection from './components/sections/FourthSection.vue'
import SecondSection from './components/sections/SecondSection.vue'
import SixthSection from './components/sections/SixthSection.vue'
import ThirdSection from './components/sections/ThirdSection.vue'
import StatusUpdater from './components/StatusUpdater.vue'

interface WasteManagementEditProps {
  data: Omit<JobOrder, 'serviceable'> & { serviceable: Form4 }
  employees?: GroupedEmployeesByAccountRole[]
  trucks?: Truck[]
}

const props = defineProps<WasteManagementEditProps>()

const trucks = ref(props?.trucks)

provide('trucks', readonly(trucks))

const serviceDate = new Date(props.data.dateTime)

const { can } = usePermissions()
const { canUpdateProposalInformation } = useWasteManagementStages()

const canUpdateProposal = computed(() => {
  return canUpdateProposalInformation(props.data.status)
})

const form = useForm<Record<string, any>>({
  date_time: serviceDate.toISOString(),
  time: serviceDate.toLocaleTimeString(undefined, {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  }),
  client: props.data.client,
  address: props.data.address,
  department: props.data.department,
  contact_position: props.data.contactPosition,
  contact_person: props.data.contactPerson,
  contact_no: props.data.contactNo,
  payment_date: props.data.serviceable?.paymentDate,
  payment_type: props.data.serviceable?.form3?.paymentType,
  bid_bond: props.data.serviceable?.bidBond,
  or_number: props.data.serviceable?.orNumber,
  status: props.data.status,
  approved_date: props.data.serviceable?.form3?.approvedDate,
})

watch([props.data, () => props?.trucks], ([newValue, newTrucks]) => {
  trucks.value = newTrucks
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
})

const onSubmit = () => {
  form.patch(
    route('job_order.waste_management.update', props.data.serviceable.id),
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
      ...(canCorrectProposalInformation(props.data.status)
        ? {
            payment_date: new Date(data.payment_date).toLocaleString(),
            or_number: data.or_number,
            bid_bond: data.bid_bond,
            payment_type: data.payment_type,
            approved_date: new Date(data.approved_date).toLocaleString(),
          }
        : {}),
    }))
    .post(route('job_order.correction.store', props.data.ticket), {
      onSuccess: (page: any) => {
        form.reset()
        isEditing.value = false
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
  manualStatuses.includes(props.data.status),
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
    title: props.data.ticket,
    href: '#',
  },
]

const unapprovedCorrections = computed(() => {
  return props.data.corrections?.find((correction) => !correction.approvedAt)
})
</script>

<template>
  <Head :title="data.ticket" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <StickyPageHeader>
        <CorrectionRequestBanner :correction="unapprovedCorrections" />
        <div
          class="mb-3 flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center"
        >
          <TicketHeader :job-order="data" />
          <div
            v-if="can('create:job_order_correction')"
            class="flex h-8 items-center gap-2"
          >
            <div
              v-if="canManuallyUpdate"
              class="ml-auto"
            >
              <StatusUpdater
                :status="data.status"
                :ticket="data.ticket"
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
                <RequestCorrectionButton v-model:is-editing="isEditing" />
              </div>
              <ArchiveJobOrder :jobOrder="data" />
            </div>
          </div>
        </div>
      </StickyPageHeader>
      <div class="my-4 flex flex-col gap-4 rounded-xl">
        <div class="mb-3 flex items-center">
          <div class="flex w-full flex-col">
            <form class="grid gap-y-6">
              <div>
                <FirstSection
                  :is-editing="isEditing && can('update:job_order')"
                  :is-service-type-disabled="true"
                  v-model:service-type="data.serviceableType"
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
                  :status="data.status"
                  :dispatcher="data.serviceable?.dispatcher"
                  :appraisers="data.serviceable.appraisers"
                  :appraised-date="data.serviceable?.form3?.appraisedDate"
                  :serviceable-id="data.serviceable.id"
                  :grouped-employees="employees"
                  @load-employees="loadEmployees"
                />
              </div>
              <div class="mt-2">
                <Separator class="mb-3 w-full" />
                <ThirdSection
                  :is-editing="isEditing && canUpdateProposal"
                  :is-submit-btn-disabled="form.processing"
                  :status="data.status"
                  :errors="form.errors"
                  v-model:payment-type="form.payment_type"
                  v-model:bid-bond="form.bid_bond"
                  v-model:or-number="form.or_number"
                  v-model:payment-date="form.payment_date"
                  v-model:approved-date="form.approved_date"
                  @on-submit="onSubmit"
                  @on-cancel-submit="form.cancel()"
                />
              </div>
              <div class="mt-2">
                <Separator class="mb-3 w-full" />
                <FourthSection
                  :status="data.status"
                  :starting-date="data.serviceable?.form3?.from"
                  :ending-date="data.serviceable?.form3?.to"
                  :serviceable-id="data.serviceable.id"
                  :dispatcher="data.serviceable?.dispatcher"
                />
              </div>
              <div
                v-if="data.serviceable.form3?.haulings?.length"
                class="mt-2"
              >
                <Separator class="mb-3 w-full" />
                <FifthSection
                  :status="data.status"
                  :haulings="data.serviceable.form3?.haulings"
                  :grouped-employees="employees"
                  :serviceable-id="data.serviceable.id"
                  @load-employees="loadEmployees"
                />
              </div>
              <div v-if="isEditing">
                <Separator class="col-[1/-1] mb-3 w-full" />
                <SixthSection
                  ref="sixthSection"
                  :status="data.status"
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
    </MainContainer>
  </AppLayout>
</template>
