<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { getInitials } from '@/composables/useInitials'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import {
  JobOrderStatuses,
  type JobOrderStatus,
} from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import { Employee, JobOrder, type BreadcrumbItem } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { Pencil, X } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import FifthSection from './components/sections/FifthSection.vue'
import FirstSection from './components/sections/FirstSection.vue'
import FourthSection from './components/sections/FourthSection.vue'
import SecondSection from './components/sections/SecondSection.vue'
import SixthSection from './components/sections/SixthSection.vue'
import ThirdSection from './components/sections/ThirdSection.vue'
import StatusUpdater from './components/StatusUpdater.vue'

interface WasteManagementEditProps {
  jobOrder: JobOrder
  employees?: Employee[]
}

const { jobOrder, employees } = defineProps<WasteManagementEditProps>()

const serviceDate = new Date(jobOrder.dateTime).toISOString()

const timeRange = new Date(serviceDate).toLocaleTimeString(undefined, {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false,
})

const { can } = usePermissions()
const {
  isForAppraisal,
  canUpdateProposalInformation,
  canUpdateHaulingDuration,
} = useWasteManagementStages()

const { serviceable: form4 } = jobOrder
const { form3 } = form4

const form = useForm({
  payment_date: form4?.paymentDate,
  payment_type: form3?.paymentType,
  bid_bond: form4.bidBond,
  or_number: form4.orNumber,
  status: jobOrder.status,
  appraised_date: form3?.appraisedDate,
  approved_date: form3?.approvedDate,
  appraisers: form4?.appraisers,
  haulings: form3?.haulings,
  from: form3?.from,
  to: form3?.to,
  remarks: '',
})

watch(
  () => jobOrder,
  (newValue) => {
    const { serviceable: service } = newValue
    const form3 = service.form3

    form.payment_date = service.paymentDate
    form.payment_type = form3.paymentType
    form.bid_bond = service.bidBond
    form.or_number = service.orNumber
    form.status = newValue.status
    form.appraised_date = form3.appraisedDate
    form.approved_date = form3.approvedDate
    form.appraisers = service.appraisers
    form.haulings = form3.haulings
    form.from = form3.from
    form.to = form3.to
  },
)

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => jobOrder.status === s.id),
)

const onSubmit = () => {
  form.patch(
    route('job_order.waste_management.update', jobOrder.serviceable.id),
    {
      preserveScroll: true,
    },
  )
}

const statusUpdater = ref()
const statusForm = useForm({})

const markAsUpdate = (nextStep: JobOrderStatus) => {
  statusForm
    .transform(() => ({ status: nextStep }))
    .patch(route('job_order.update', jobOrder.ticket), {
      preserveScroll: true,
      onSuccess: () => (statusUpdater.value.isOpen = false),
    })
}

const markAsStop = (stopStep: JobOrderStatus, reason: string) => {
  statusForm
    .transform(() => ({
      status: stopStep,
      reason: reason,
    }))
    .post(route('job_order.cancel.create', jobOrder.ticket), {
      preserveScroll: true,
      onError: () => statusUpdater.value.reasonInput.$el.focus(),
      onSuccess: () => (statusUpdater.value.isOpen = false),
    })
}

const loadEmployees = () => router.reload({ only: ['employees'] })

const isEditing = ref<boolean>(false)

const manualStatuses: Array<JobOrderStatus> = ['for viewing', 'for proposal']

const canManuallyUpdate = computed(() =>
  manualStatuses.includes(jobOrder.status),
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
    title: jobOrder.ticket,
    href: '#',
  },
]
</script>

<template>
  <Head :title="jobOrder.ticket" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div
      class="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-background p-6 shadow-sm"
    >
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-4">
          <h3 class="scroll-m-20 text-3xl font-semibold text-primary">
            Ticket:
            <span class="tracking-tighter text-muted-foreground">
              {{ jobOrder.ticket }}
            </span>
          </h3>
          <Badge
            :variant="jobOrderStatus?.badge"
            class="overflow-hidden truncate text-ellipsis rounded-full"
          >
            {{ jobOrderStatus?.label }}
          </Badge>
        </div>
        <div class="flex items-center gap-2 text-sm text-muted-foreground">
          <Avatar class="h-7 w-7 shrink-0 rounded-full">
            <AvatarImage
              v-if="jobOrder.creator?.account?.avatar"
              :src="jobOrder.creator.account.avatar"
              :alt="jobOrder.creator.fullName"
            />
            <AvatarFallback>
              {{ getInitials(jobOrder.creator?.fullName) }}
            </AvatarFallback>
          </Avatar>
          <div class="flex items-center gap-1">
            <span>{{ `${jobOrder.creator?.fullName}` }}</span>
            <span class="mx-1">•</span>
            <span>{{
              new Date(jobOrder.createdAt).toLocaleString('en-ph', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })
            }}</span>
          </div>
        </div>
      </div>
      <div
        v-if="can('submit:job_order_correction')"
        class="flex h-8 items-center gap-2"
      >
        <div
          v-if="canManuallyUpdate"
          class="ml-auto"
        >
          <StatusUpdater
            ref="statusUpdater"
            :status="jobOrder.status"
            :form="statusForm"
            @mark-as-update="markAsUpdate"
            @mark-as-stop="markAsStop"
          />
        </div>
        <Separator
          v-if="canManuallyUpdate"
          orientation="vertical"
        />
        <Button
          v-if="!isEditing"
          variant="outline"
          @click="() => (isEditing = !isEditing)"
        >
          <Pencil class="mr-2" />
          Request Ticket Correction
        </Button>
        <template v-else>
          <Button
            variant="outline"
            @click="() => (isEditing = !isEditing)"
          >
            <X class="mr-2" />
            Cancel Ticket Correction
          </Button>
        </template>
      </div>
    </div>
    <div class="my-4 flex h-full flex-1 flex-col gap-4 rounded-xl">
      <div class="mb-3 flex items-center">
        <div class="flex w-full flex-col">
          <form
            @submit.prevent="onSubmit"
            class="mx-7 grid gap-y-6"
          >
            <div>
              <FirstSection
                :is-editing="isEditing && can('update:job_order')"
                :is-service-type-disabled="true"
                v-model:service-type="jobOrder.serviceableType"
                v-model:service-date="serviceDate"
                v-model:service-time="timeRange"
                v-model:client="jobOrder.client"
                v-model:address="jobOrder.address"
                v-model:department="jobOrder.department"
                v-model:contact-position="jobOrder.contactPosition"
                v-model:contact-person="jobOrder.contactPerson"
                v-model:contact-number="jobOrder.contactNo"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <SecondSection
                :can-edit="
                  can('assign:appraisers') && isForAppraisal(jobOrder.status)
                "
                :status="jobOrder.status"
                :errors="form.errors"
                :dispatcher="form4?.dispatcher"
                :is-submit-btn-disabled="form.processing"
                v-model:appraisers="form.appraisers"
                v-model:appraised-date="form.appraised_date"
                :employees="employees"
                @load-employees="loadEmployees"
                @on-cancel-submit="form.cancel()"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <ThirdSection
                :is-editing="
                  isEditing && canUpdateProposalInformation(jobOrder.status)
                "
                :is-submit-btn-disabled="form.processing"
                :status="jobOrder.status"
                :errors="form.errors"
                v-model:payment-type="form.payment_type"
                v-model:bid-bond="form.bid_bond"
                v-model:or-number="form.or_number"
                v-model:payment-date="form.payment_date"
                v-model:approved-date="form.approved_date"
                :employees="employees"
                @on-cancel-submit="form.cancel()"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <FourthSection
                :can-edit="canUpdateHaulingDuration(jobOrder.status)"
                :status="jobOrder.status"
                :is-submit-btn-disabled="form.processing"
                :errors="form.errors"
                v-model:starting-date="form.from"
                v-model:ending-date="form.to"
                @on-cancel-submit="form.cancel()"
              />
            </div>
            <div
              v-if="form.haulings?.length"
              class="mt-2"
            >
              <Separator class="mb-3 w-full" />
              <FifthSection
                :status="jobOrder.status"
                :is-submit-btn-disabled="form.processing"
                v-model:haulings="form.haulings"
                :employees="employees"
                @load-employees="loadEmployees"
                @on-cancel-submit="form.cancel()"
              />
            </div>
            <div v-if="isEditing">
              <Separator class="col-[1/-1] mb-3 w-full" />
              <SixthSection
                ref="sixthSection"
                :status="jobOrder.status"
                v-model:remarks="form.remarks"
              />
            </div>
            <div
              v-if="can('update:job_order') && isEditing"
              class="col-[1/-1] mt-4 flex w-full items-center"
            >
              <div class="ml-auto space-x-3">
                <Button
                  type="button"
                  variant="outline"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  variant="default"
                  :disabled="form.processing"
                >
                  Submit Corrections
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
