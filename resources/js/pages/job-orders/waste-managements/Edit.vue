<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import AppLayout from '@/layouts/AppLayout.vue'
import { Employee, JobOrder, type BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/vue3'
import FirstSection from './components/FirstSection.vue'
import FourthSection from './components/FourthSection.vue'
import SecondSection from './components/SecondSection.vue'
import ThirdSection from './components/ThirdSection.vue'

interface WasteManagementEditProps {
  jobOrder: JobOrder
  employees: Employee[]
}

const props = defineProps<WasteManagementEditProps>()

const { jobOrder, employees } = props
const { serviceable: form4 } = jobOrder
const { form3 } = form4

const serviceDate = new Date(jobOrder.dateTime).toISOString()

const timeRange = new Date(serviceDate).toLocaleTimeString(undefined, {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false,
})

const form = useForm({
  payment_date: form4?.paymentDate,
  payment_type: form3?.paymentType,
  bid_bond: form4?.bidBond,
  or_number: form4?.orNumber,
  status: jobOrder.status,
  appraised_date: form3?.appraisedDate,
  approved_date: form3?.approvedDate,
  team_leader: form3?.teamLeader,
  team_driver: form3?.teamDriver,
  safety_officer: form3?.safetyOfficer,
  team_mechanic: form3?.teamMechanic,
  truck_no: form3?.truckNo,
  haulers: form3?.haulers,
  appraisers: form4?.appraisers,
})
// console.log(form4.paymentDate)

const onSubmit = () => {
  console.log(form)
  form
    .transform((data) => ({
      ...data,
      appraisers: data.appraisers?.map((a: Employee) => a.id),
      haulers: data.haulers?.map((h: Employee) => h.id),
      team_leader: data.team_leader?.id,
      team_driver: data.team_driver?.id,
      safety_officer: data.safety_officer?.id,
      team_mechanic: data.team_mechanic?.id,
    }))
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
    title: 'Edit',
    href: '#',
  },
]
</script>

<template>
  <Head title="Edit Job Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-3 py-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="mb-3 flex items-center">
          <div class="flex flex-col">
            <h3 class="mb-8 scroll-m-20 text-3xl font-bold">Edit Job Order</h3>
            <form
              @submit.prevent="onSubmit"
              class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6"
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
              <Separator class="col-[1/-1] my-2 w-full" />
              <FourthSection
                v-model:teamLeader="form.team_leader"
                v-model:safetyOfficer="form.safety_officer"
                v-model:teamDriver="form.team_driver"
                v-model:teamMechanic="form.team_mechanic"
                v-model:haulers="form.haulers"
                v-model:truckNumber="form.truck_no"
                :employees="employees"
              />
              <Separator class="col-[1/-1] my-2 w-full" />
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
