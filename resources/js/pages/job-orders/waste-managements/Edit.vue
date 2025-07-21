<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { getInitials } from '@/composables/useInitials'
import { useJobOrderStatus } from '@/composables/useJobOrderStatus'
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import {
  JobOrderStatuses,
  type JobOrderStatus,
} from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import { Employee, Form3Hauling, JobOrder, type BreadcrumbItem } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { ChevronRight, Pencil, X } from 'lucide-vue-next'
import { computed, nextTick, ref, useTemplateRef, watch } from 'vue'
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

const { can, cannot } = usePermissions()
const {
  isForAppraisal,
  canUpdateProposalInformation,
  canUpdateHaulingDuration,
} = useWasteManagementStages()
const { getCancelledStatuses } = useJobOrderStatus()

const sixthSectionRef = useTemplateRef('sixthSection')

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

watch(() => jobOrder, (newValue) => {
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
})

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => jobOrder.status === s.id),
)

const handleStatusChange = async (value: JobOrderStatus) => {
  isStatusPopoverOpen.value = false

  const isCancelled = getCancelledStatuses.value
    .map((cs) => cs.id)
    .includes(value)

  if (isCancelled) {
    await nextTick()
    sixthSectionRef.value?.$el.scrollIntoView({ behavior: 'smooth' })
    setTimeout(() => sixthSectionRef.value?.remarksInput?.$el.focus(), 600) // hack :))
  }
}

/**
 * haulings = []
 */
const onSubmit = () => {
  form
    // .transform((data) => ({
    //   ...data,
    //   haulings: data.haulings.map((hauling: Form3Hauling) => {
    //     return {
    //       assigned
    //     }
    //   })
    // }))
    .patch(route('job_order.waste_management.update', jobOrder.serviceable.id), {
        preserveScroll: true,
      },
    )
}

const markAsUpdate = (nextStep: JobOrderStatus) => {
  router.patch(
    route('job_order.update', jobOrder.ticket),
    {
      status: nextStep,
    },
    {
      preserveScroll: true,
    },
  )
}

const loadEmployees = () => {
  router.reload({
    only: ['employees'],
  })
}

const isStatusPopoverOpen = ref<boolean>(false)

const onStatusPopoverToggle = () => {
  cannot('update:job_order')
    ? (isStatusPopoverOpen.value = false)
    : (isStatusPopoverOpen.value = !isStatusPopoverOpen.value)
}

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
          <h3 class="scroll-m-20 text-3xl text-primary font-semibold">
            Ticket:
            <span class="tracking-tighter text-muted-foreground">
              {{ jobOrder.ticket }}
            </span>
          </h3>
          <Popover
            :open="isStatusPopoverOpen"
            @update:open="onStatusPopoverToggle"
          >
            <PopoverTrigger
              as-child
              class="flex items-center gap-1"
            >
              <Button
                variant="ghost"
                :class="[
                  'h-auto w-auto rounded-full p-1',
                  { 'hover:bg-0 cursor-default': cannot('update:job_order') },
                ]"
              >
                <Badge
                  :variant="jobOrderStatus?.badge"
                  class="overflow-hidden truncate text-ellipsis rounded-full"
                >
                  {{ jobOrderStatus?.label }}
                </Badge>
                <ChevronRight
                  v-if="can('update:job_order')"
                  class="h-4 w-4"
                />
              </Button>
            </PopoverTrigger>
            <PopoverContent
              class="overflow-auto p-0"
              align="start"
              side="right"
            >
              <Command>
                <CommandInput placeholder="Filter status badges" />
                <CommandList>
                  <CommandEmpty> No results found. </CommandEmpty>
                  <CommandGroup>
                    <div v-for="jOStatus in JobOrderStatuses">
                      <CommandItem
                        v-if="jOStatus.id !== jobOrderStatus?.id"
                        :key="jOStatus.id"
                        :value="jOStatus"
                        @select="handleStatusChange(jOStatus.id)"
                      >
                        <Badge :variant="jOStatus.badge">
                          {{ jOStatus.label }}
                        </Badge>
                      </CommandItem>
                    </div>
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
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
            :status="jobOrder.status"
            @mark-as-update="markAsUpdate"
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
                :dispatcher="form4.dispatcher"
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
            <div class="mt-2">
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
            <div
              v-if="
                getCancelledStatuses.map((cs) => cs.id).includes(jobOrder.status) ||
                isEditing
              "
            >
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
