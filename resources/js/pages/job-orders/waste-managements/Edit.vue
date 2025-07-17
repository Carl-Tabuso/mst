<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import { Employee, JobOrder, type BreadcrumbItem } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { Pencil } from 'lucide-vue-next'
import { computed } from 'vue'
import FirstSection from './components/FirstSection.vue'
import FourthSection from './components/FourthSection.vue'
import SecondSection from './components/SecondSection.vue'
import ThirdSection from './components/ThirdSection.vue'

interface WasteManagementEditProps {
  jobOrder: JobOrder
  employees?: Employee[]
}

const { jobOrder, employees } = defineProps<WasteManagementEditProps>()

const { serviceable: form4 } = jobOrder
const { form3 } = form4

const serviceDate = new Date(jobOrder.dateTime).toISOString()

const timeRange = new Date(serviceDate).toLocaleTimeString(undefined, {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false,
})

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => jobOrder.status === s.id),
)

const form = useForm({
  payment_date: form4?.paymentDate,
  payment_type: form3?.paymentType,
  bid_bond: form4?.bidBond,
  or_number: form4?.orNumber,
  status: jobOrder.status,
  appraised_date: form3?.appraisedDate,
  approved_date: form3?.approvedDate,
  appraisers: form4?.appraisers,
  haulings: form3?.haulings,
})

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
    .patch(route('job_order.waste_management.update', form4.id))
}

const firstSectionBindings = {
  isServiceTypeInputDisabled: true,
  isServiceDateInputDisabled: true,
  isServiceTimeInputDisabled: true,
  isClientInputDisabled: true,
  isAddressInputDisabled: true,
  isDepartmentInputDisabled: true,
  isContactPositionInputDisabled: true,
  isContactPersonInputDisabled: true,
  isContactNumberInputDisabled: true,
}

const loadEmployees = () => {
  router.reload({
    only: ['employees'],
  })
}

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
      class="sticky top-0 z-10 flex items-start justify-between bg-background p-6"
    >
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-4">
          <h3 class="scroll-m-20 text-3xl font-semibold">
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
            <span>{{ jobOrder.creator?.fullName }}</span>
            <span class="mx-1">•</span>
            <span>{{ formatToDateString(jobOrder.createdAt) }}</span>
          </div>
        </div>
      </div>
      <Button
        variant="outline"
        class="rounded-full"
      >
        <Pencil class="mr-2" />
        Edit Job Order
      </Button>
    </div>
    <div class="px-3 py-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="mb-3 flex items-center">
          <div class="flex w-full flex-col">
            <form
              @submit.prevent="onSubmit"
              class="grid gap-y-6"
            >
              <FirstSection
                v-bind="firstSectionBindings"
                v-model:serviceType="jobOrder.serviceableType"
                v-model:serviceDate="serviceDate"
                v-model:serviceTime="timeRange"
                v-model:client="jobOrder.client"
                v-model:address="jobOrder.address"
                v-model:department="jobOrder.department"
                v-model:contactPosition="jobOrder.contactPosition"
                v-model:contactPerson="jobOrder.contactPerson"
                v-model:contactNumber="jobOrder.contactNo"
              />
              <Separator class="col-[1/-1] my-2 w-full" />
              <SecondSection
                v-model:appraisers="form.appraisers"
                v-model:appraisedDate="form.appraised_date"
                :employees="employees"
                @load-employees="loadEmployees"
              />
              <Separator class="col-[1/-1] my-2 w-full" />
              <ThirdSection
                v-model:paymentType="form.payment_type"
                v-model:bidBond="form.bid_bond"
                v-model:orNumber="form.or_number"
                v-model:paymentDate="form.payment_date"
                v-model:approvedDate="form.approved_date"
                v-model:status="form.status"
                :employees="employees"
              />
              <Separator class="col-[1/-1] mt-2 w-full" />
              <FourthSection
                v-model:haulings="form.haulings"
                :employees="employees"
                @load-employees="loadEmployees"
              />
              <div class="col-[1/-1] flex w-full items-center">
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
    </div>
  </AppLayout>
</template>
