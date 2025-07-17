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
import { usePermissions } from '@/composables/usePermissions'
import { JobOrderStatuses, type JobOrderStatus } from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import { Employee, JobOrder, type BreadcrumbItem } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { ChevronRight, Pencil, X } from 'lucide-vue-next'
import { computed, nextTick, Ref, ref, useTemplateRef } from 'vue'
import FifthSection from './components/sections/FifthSection.vue'
import FirstSection from './components/sections/FirstSection.vue'
import FourthSection from './components/sections/FourthSection.vue'
import SecondSection from './components/sections/SecondSection.vue'
import ThirdSection from './components/sections/ThirdSection.vue'
import SixthSection from './components/sections/SixthSection.vue'
import { useJobOrderStatus } from '@/composables/useJobOrderStatus'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'

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
  canUpdateHaulingDuration
} = useWasteManagementStages()
const { getCancelledStatuses } = useJobOrderStatus()

const status = ref<string>(jobOrder.status) as Ref<JobOrderStatus>
const sixthSectionRef = useTemplateRef('sixthSection')

const { serviceable: form4 } = jobOrder
const { form3 } = form4

const form = useForm({
  payment_date: form4?.paymentDate,
  payment_type: form3?.paymentType,
  bid_bond: form4.bidBond,
  or_number: form4.orNumber,
  status: status.value,
  appraised_date: form3?.appraisedDate,
  approved_date: form3?.approvedDate,
  appraisers: form4?.appraisers,
  haulings: form3?.haulings,
  from: form3?.from,
  to: form3?.to,
  remarks: ''
})

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => status.value === s.id),
)

const handleStatusChange = async(value: JobOrderStatus) => {
  status.value = value
  isStatusPopoverOpen.value = false
  
  const isCancelled = getCancelledStatuses.value.map(cs => cs.id).includes(value)

  if (isCancelled) {
    await nextTick()
    sixthSectionRef.value?.$el.scrollIntoView({ behavior: 'smooth' })
    setTimeout(() => sixthSectionRef.value?.remarksInput?.$el.focus(), 600) // hack :))
  }
}

const onSubmit = () => {
  console.log(form)
  form
    // .transform((data) => ({
    //   ...data,
    //   appraisers: data.appraisers?.map((a: Employee) => a.id),
    //   haulers: data.haulers?.map((h: Employee) => h.id),
    //   team_leader: data.team_leader?.id,
    //   team_driver: data.team_driver?.id,
    //   safety_officer: data.safety_officer?.id,
    //   team_mechanic: data.team_mechanic?.id,
    // }))
    .patch(route('job_order.waste_management.update', jobOrder.serviceable.id))
}

const loadEmployees = () => {
  router.reload({
    only: ['employees'],
  })
}

const isStatusPopoverOpen = ref<boolean>(false)

const onStatusPopoverToggle = () => {
  cannot('update:job_order')
    ? isStatusPopoverOpen.value = false
    : isStatusPopoverOpen.value = ! isStatusPopoverOpen.value
}

const isEditing = ref<boolean>(false)

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
      class="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-background p-6"
    >
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-4">
          <h3 class="scroll-m-20 text-3xl font-semibold">
            Ticket:
            <span class="tracking-tighter text-muted-foreground">
              {{ jobOrder.ticket }}
            </span>
          </h3>
          <Popover :open="isStatusPopoverOpen" @update:open="onStatusPopoverToggle">
            <PopoverTrigger
              as-child
              class="flex items-center gap-1"
            >
              <Button
                variant="ghost"
                :class="['h-auto w-auto rounded-full p-1',
                  { 'cursor-default hover:bg-0': cannot('update:job_order') }
                ]"
              >
                <Badge
                  :variant="jobOrderStatus?.badge"
                  class="overflow-hidden truncate text-ellipsis rounded-full"
                >
                  {{ jobOrderStatus?.label }}
                </Badge>
                <ChevronRight v-if="can('update:job_order')" class="h-4 w-4" />
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
            <span>{{ jobOrder.creator?.fullName }}</span>
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
      <template v-if="can('submit:job_order_correction')">
        <Button
          v-if="! isEditing"
          variant="outline"
          @click="() => (isEditing = true)"
        >
          <Pencil class="mr-2" />
          Request Ticket Correction
        </Button>
        <template v-else>
          <Button
            variant="outline"
            @click="() => (isEditing = false)"
          >
            <X class="mr-2" />
            Cancel Ticket Correction
          </Button> 
        </template>
      </template>
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
                :can-edit="can('assign:appraisers') && isForAppraisal(jobOrder)"
                v-model:appraisers="form.appraisers"
                v-model:appraised-date="form.appraised_date"
                :employees="employees"
                @load-employees="loadEmployees"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <ThirdSection
                :is-editing="isEditing && canUpdateProposalInformation(jobOrder.status as JobOrderStatus)"
                v-model:payment-type="form.payment_type"
                v-model:bid-bond="form.bid_bond"
                v-model:or-number="form.or_number"
                v-model:payment-date="form.payment_date"
                v-model:approved-date="form.approved_date"
                :employees="employees"
              />
            </div>
            <div class="mt-2">
              <Separator class="mb-3 w-full" />
              <FourthSection
                :can-edit="canUpdateHaulingDuration(jobOrder.status as JobOrderStatus)"
                v-model:starting-date="form.from"
                v-model:ending-date="form.to"
              />
            </div>
            <div class="mt-2">
              <Separator class="col-[1/-1] mb-3 w-full" />
              <FifthSection
                :can-edit="can('assign:hauling_personnel')"
                v-model:haulings="form.haulings"
                :employees="employees"
                @load-employees="loadEmployees"
              />
            </div>
            <div v-if="getCancelledStatuses.map(cs => cs.id).includes(status)" class="mt-2">
              <Separator class="col-[1/-1] mb-3 w-full" />
              <SixthSection
                ref="sixthSection"
                :status="status"
                v-model:remarks="form.remarks"
              />
            </div>
            <div class="col-[1/-1] mt-4 flex w-full items-center pr-6">
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
                >
                  Update Job Order
                </Button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
