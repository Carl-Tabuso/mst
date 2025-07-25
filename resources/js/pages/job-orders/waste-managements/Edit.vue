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
import { toast } from 'vue-sonner'

interface WasteManagementEditProps {
  jobOrder: JobOrder
  employees?: Employee[]
}

const props = defineProps<WasteManagementEditProps>()

const serviceDate = new Date(props.jobOrder.dateTime).toISOString()

const timeRange = new Date(serviceDate).toLocaleTimeString(undefined, {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false,
})

const { can } = usePermissions()
const { canUpdateProposalInformation } = useWasteManagementStages()

const form = useForm({
  payment_date: props.jobOrder.serviceable?.paymentDate,
  payment_type: props.jobOrder.serviceable?.form3?.paymentType,
  bid_bond: props.jobOrder.serviceable?.bidBond,
  or_number: props.jobOrder.serviceable?.orNumber,
  status: props.jobOrder.status,
  approved_date: props.jobOrder.serviceable?.form3?.approvedDate,
  remarks: '',
})

watch(
  () => props.jobOrder,
  (newValue) => {
    const { serviceable: service } = newValue
    const form3 = service?.form3

    form.payment_date = service?.paymentDate
    form.payment_type = form3?.paymentType
    form.bid_bond = service.bidBond
    form.or_number = service.orNumber
    form.status = newValue.status
    form.approved_date = form3?.approvedDate
  },
)

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => props.jobOrder.status === s.id),
)

const onSubmit = () => {
  form.patch(
    route('job_order.waste_management.update', props.jobOrder.serviceable.id),
    {
      preserveScroll: true,
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message, {
          position: 'top-right'
        })
      }
    },
  )
}

const loadEmployees = () => router.reload({ only: ['employees'] })

const isEditing = ref<boolean>(false)

const manualStatuses: Array<JobOrderStatus> = [
  'for viewing',
  'for proposal',
  'hauling in-progress'
]

const canManuallyUpdate = computed(() =>
  manualStatuses.includes(props.jobOrder.status),
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
    title: props.jobOrder.ticket,
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
          <h3 class="scroll-m-20 text-3xl font-bold text-primary">
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
        <div class="flex items-center gap-2">
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
          <div class="flex items-center gap-1 text-sm text-muted-foreground">
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
            :status="jobOrder.status"
            :ticket="jobOrder.ticket"
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
                :status="jobOrder.status"
                :dispatcher="jobOrder.serviceable?.dispatcher"
                :appraisers="jobOrder.serviceable.appraisers"
                :appraised-date="jobOrder.serviceable?.form3?.appraisedDate"
                :serviceable-id="jobOrder.serviceable.id"
                :employees="employees"
                @load-employees="loadEmployees"
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
                @on-submit="onSubmit"
                @on-cancel-submit="form.cancel()"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <FourthSection
                :status="jobOrder.status"
                :starting-date="jobOrder.serviceable?.form3?.from"
                :ending-date="jobOrder.serviceable?.form3?.to"
                :serviceable-id="jobOrder.serviceable.id"
              />
            </div>
            <div
              v-if="jobOrder.serviceable.form3?.haulings?.length"
              class="mt-2"
            >
              <Separator class="mb-3 w-full" />
              <FifthSection
                :status="jobOrder.status"
                :haulings="jobOrder.serviceable.form3?.haulings"
                :employees="employees"
                :serviceable-id="jobOrder.serviceable.id"
                @load-employees="loadEmployees"
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
