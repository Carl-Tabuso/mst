<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Employee, Form4 } from '@/types'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import FirstSection from './components/FirstSection.vue'
import SecondSection from './components/SecondSection.vue'
import ThirdSection from './components/ThirdSection.vue'
import FourthSection from './components/FourthSection.vue'

interface WasteManagementEditProps {
  form4: Form4
  employees: Employee[]
}

const props = defineProps<WasteManagementEditProps>()

const { form4, employees } = props
const { form3, jobOrder } = form4

const teamLeader = ref<Employee| undefined>(form3?.teamLeader)
const safetyOfficer = ref<Employee | undefined>(form3?.safetyOfficer)
const teamDriver = ref<Employee | undefined>(form3?.teamDriver)
const teamMechanic = ref<Employee | undefined>(form3?.teamMechanic)
const haulers = ref<Employee[] | undefined>(form3?.haulers)
const appraisers = ref<Employee[] | undefined>(form4?.appraisers)
const serviceDate = ref(new Date(jobOrder?.dateTime || new Date()).toISOString())
const appraisedDate = ref<string | undefined>(new Date(jobOrder?.dateTime || new Date()).toISOString())
const paymentDate = ref<string | undefined>(new Date(form4?.paymentDate || new Date()).toISOString())
const approvedDate = ref<string | undefined>(new Date(form3?.approvedDate || new Date()).toISOString())
const status = ref<string>(jobOrder.status)

const timeRange = new Date(serviceDate.value).toLocaleTimeString(undefined, {
  hour: '2-digit',
  minute: '2-digit',
  hour12: false
})

const serviceTime = ref(timeRange)

const form = useForm({
  service_type: jobOrder.serviceableType,
  client: jobOrder.client,
  address: jobOrder.address,
  department: jobOrder.department,
  contact_position: jobOrder.contactPosition,
  contact_person: jobOrder.contactPerson,
  contact_no: jobOrder.contactNo,
  payment_date: paymentDate.value,
  payment_type: form3?.paymentType,
  bid_bond: form4?.bidBond,
  or_number: form4?.orNumber,
  status: status.value,
  appraised_date: appraisedDate.value,
  approved_date: approvedDate.value,
  team_leader: teamLeader.value?.id,
  team_driver: teamDriver.value?.id,
  safety_officer: safetyOfficer.value?.id,
  team_mechanic: teamMechanic.value?.id,
  truck_no: form3?.truckNo,
  haulers: haulers.value,
  appraisers: appraisers.value
})

const onSubmit = () => {
  const [ hours, min ] = serviceTime.value.split(':')
  const epoch = new Date(serviceDate.value).setHours(Number(hours), Number(min))
  // found this abomination online. I need to format the date to Y-m-d H:i:s
  const formatted = new Date(epoch).toJSON().split('.')[0].split('T').join(' ')

  form.transform((data) => ({
    ...data,
    date_time: formatted,
  }))

  form.patch(route('job_order.waste_management.update', form4.id))
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders'
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: 'Edit',
    href: '#'
  }
]
</script>

<template>
  <Head title="Edit Job Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-3 py-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex items-center mb-3">
          <div class="flex flex-col">
            <h3 class="scroll-m-20 text-3xl font-bold mb-8">
              Edit Job Order
            </h3>
            <form @submit.prevent="onSubmit" class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
              <FirstSection
                v-model:serviceType="form.service_type"
                v-model:serviceDate="serviceDate"
                v-model:serviceTime="serviceTime"
                v-model:client="form.client"
                v-model:address="form.address"
                v-model:department="form.department"
                v-model:contactPosition="form.contact_position"
                v-model:contactPerson="form.contact_person"
                v-model:contactNumber="form.contact_no"
              />
              <Separator class="my-2 w-full col-[1/-1]" />
              <SecondSection
                v-model:appraisers="appraisers"
                v-model:appraisedDate="appraisedDate"
                :employees="employees"
              />
              <Separator class="my-2 w-full col-[1/-1]" />
              <ThirdSection
                v-model:paymentType="form.payment_type"
                v-model:bidBond="form.bid_bond"
                v-model:orNumber="form.or_number"
                v-model:paymentDate="paymentDate"
                v-model:approvedDate="approvedDate"
                v-model:status="status"
                :employees="employees"
              />
              <Separator class="my-2 w-full col-[1/-1]" />
              <FourthSection
                v-model:teamLeader="teamLeader"
                v-model:safetyOfficer="safetyOfficer"
                v-model:teamDriver="teamDriver"
                v-model:teamMechanic="teamMechanic"
                v-model:haulers="haulers"
                v-model:truckNumber="form.truck_no"
                :employees="employees"
              />
              <Separator class="my-2 w-full col-[1/-1]" />
              <div class="w-full col-[1/-1] flex items-center">
                <div class="ml-auto space-x-3">
                  <Button type="button" variant="outline">
                    Cancel
                  </Button>
                  <Button type="submit" variant="default">
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
