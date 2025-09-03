<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import AppLayout from '@/layouts/AppLayout.vue'
import JobOrderDetails from '@/pages/job-orders/components/JobOrderDetails.vue'
import MachineDetails from '@/pages/job-orders/components/MachineDetails.vue'
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue'
import { BreadcrumbItem, Employee, ITService, JobOrder } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { LoaderCircle } from 'lucide-vue-next'
import { ref } from 'vue'
import InitialOnsiteDetails from '../components/InitialOnsiteDetails.vue'

interface ShowProps {
  data: Omit<JobOrder, 'serviceable'> & { serviceable: ITService }
  technicians: Employee[]
}

const props = defineProps<ShowProps>()
console.log(props.data)

const serviceDate = new Date(props.data.dateTime)

const technician = ref<Employee | null>(
  props.data.serviceable?.technician ?? null,
)

const form = useForm({
  service_type: props.data.serviceableType,
  date_time: serviceDate.toISOString(),
  time: format(serviceDate, 'HH:mm'),
  client: props.data.client,
  address: props.data.address,
  department: props.data.department,
  contact_position: props.data.contactPosition,
  contact_person: props.data.contactPerson,
  contact_no: props.data.contactNo,
  technician: technician?.value?.id,
  machine_type: props.data.serviceable.machineType,
  model: props.data.serviceable.model,
  serial_no: props.data.serviceable.serialNo,
  tag_no: props.data.serviceable.tagNo,
  machine_problem: props.data.serviceable.machineProblem,
  initial_service_performed:
    props.data.serviceable?.initialOnsiteReport?.servicePerformed,
  recommendation: props.data.serviceable?.initialOnsiteReport?.recommendation,
  initial_machine_status:
    props.data.serviceable?.initialOnsiteReport?.machineStatus,
  report_file: props.data.serviceable?.initialOnsiteReport?.fileName,
  final_service_performed:
    props.data.serviceable?.finalOnsiteReport?.servicePerformed,
  parts_replaced: props.data.serviceable?.finalOnsiteReport?.partsReplaced,
  remarks: props.data.serviceable?.finalOnsiteReport?.remarks,
  final_machine_status:
    props.data.serviceable?.finalOnsiteReport?.machineStatus,
})

const onSubmit = () => {
  //
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Order',
    href: '/job-orders',
  },
]
</script>

<template>
  <Head title="" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="border-b border-border bg-background shadow-sm">
        <div class="mb-3 flex flex-row items-center justify-between">
          <TicketHeader :job-order="data" />
        </div>
      </div>
      <div class="mt-4">
        <form
          @submit.prevent="onSubmit"
          enctype="multipart/form-data"
          class="space-y-6"
        >
          <div>
            <JobOrderDetails
              is-service-type-disabled
              v-model:service-type="data.serviceableType"
              v-model:service-date="form.date_time"
              v-model:service-time="form.time"
              v-model:client="form.client"
              v-model:address="form.address"
              v-model:department="form.department"
              v-model:contact-position="form.contact_position"
              v-model:contact-person="form.contact_person"
              v-model:contact-number="form.contact_no"
              v-model:technician="technician"
              :technicians="technicians"
              :errors="form.errors"
            />
          </div>
          <div>
            <Separator class="mb-3" />
            <MachineDetails
              v-model:machine-type="form.machine_type"
              v-model:machine-model="form.model"
              v-model:serial-number="form.serial_no"
              v-model:tag-number="form.tag_no"
              v-model:machine-problem="form.machine_problem"
              :errors="form.errors"
            />
          </div>
          <div>
            <Separator class="mb-3" />
            <InitialOnsiteDetails
              v-model:service-performed="form.initial_service_performed"
              v-model:machine-status="form.initial_machine_status"
              v-model:recommendation="form.recommendation"
              v-model:report-file="form.report_file"
              :errors="form.errors"
            />
          </div>
          <div class="col-span-2 flex flex-row items-center justify-end gap-3">
            <Button
              type="button"
              variant="outline"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              :disabled="form.processing"
            >
              <LoaderCircle
                v-if="form.processing"
                class="animate-spin"
              />
              Submit Report
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
