<script setup lang="ts">
import CorrectionRequestBanner from '@/components/CorrectionRequestBanner.vue'
import MainContainer from '@/components/MainContainer.vue'
import StickyPageHeader from '@/components/StickyPageHeader.vue'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { formatToDateTime } from '@/composables/useDateFormatter'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { usePermissions } from '@/composables/usePermissions'
import AppLayout from '@/layouts/AppLayout.vue'
import ArchiveJobOrder from '@/pages/job-orders/components/ArchiveJobOrder.vue'
import CorrectionReason from '@/pages/job-orders/components/CorrectionReason.vue'
import JobOrderDetails from '@/pages/job-orders/components/JobOrderDetails.vue'
import MachineDetails from '@/pages/job-orders/components/MachineDetails.vue'
import RequestCorrectionButton from '@/pages/job-orders/components/RequestCorrectionButton.vue'
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue'
import { BreadcrumbItem, Employee, ITService, JobOrder } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { LoaderCircle } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import FinalOnsiteDetails from '../components/FinalOnsiteDetails.vue'
import InitialOnsiteDetails from '../components/InitialOnsiteDetails.vue'

interface EditProps {
  data: Omit<JobOrder, 'serviceable'> & { serviceable: ITService }
  regulars: Employee[]
}

const props = defineProps<EditProps>()

const technician = ref<Employee | null>(
  props.data.serviceable?.technician ?? null,
)

const technicianId = computed(() => technician.value?.id)

const serviceDate = new Date(props.data.dateTime)

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
  technician: technicianId as any,
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
  reason: '',
})

const isEditing = ref<boolean>(false)

const onSubmit = () => {
  form
    .transform((data) => ({
      ...data,
      date_time: formatToDateTime(data.date_time, data.time).toLocaleString(),
    }))
    .post(route('job_order.correction.store', props.data.ticket), {
      onStart: () => form.clearErrors,
      onSuccess: () => (isEditing.value = false),
      onFinish: () => (isEditing.value = false),
    })
}

const { statusMap } = useJobOrderDicts()

const forFinalService: boolean =
  props.data.status === statusMap['for final service'].id
const completed: boolean = props.data.status === statusMap['completed'].id

const unapprovedCorrections = computed(() => {
  return props.data.corrections?.find((correction) => !correction.approvedAt)
})

const { can } = usePermissions()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders',
  },
  {
    title: 'IT Services',
    href: '/job-orders/it-services',
  },
  {
    title: props.data.ticket,
    href: '#',
  },
]
</script>

<template>
  <Head :title="data.ticket" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <StickyPageHeader>
        <CorrectionRequestBanner :correction="unapprovedCorrections" />
        <div class="mb-3 flex flex-row items-center justify-between">
          <TicketHeader :job-order="data" />
          <div
            v-if="can('create:job_order_correction')"
            class="flex h-8 flex-row items-center gap-2"
          >
            <div class="flex gap-5">
              <RequestCorrectionButton
                v-if="!unapprovedCorrections"
                v-model:is-editing="isEditing"
              />
              <ArchiveJobOrder
                :job-order="data"
                redirect-url-after-archive="job_order.it_service.index"
              />
            </div>
          </div>
        </div>
      </StickyPageHeader>
      <div class="mt-4">
        <form
          @submit.prevent="onSubmit"
          enctype="multipart/form-data"
          class="space-y-6"
        >
          <div>
            <JobOrderDetails
              is-service-type-disabled
              :is-editing="isEditing"
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
              :technicians="regulars"
              :errors="form.errors"
            />
          </div>
          <div>
            <Separator class="mb-3" />
            <MachineDetails
              :is-editing="isEditing"
              v-model:machine-type="form.machine_type"
              v-model:machine-model="form.model"
              v-model:serial-number="form.serial_no"
              v-model:tag-number="form.tag_no"
              v-model:machine-problem="form.machine_problem"
              :errors="form.errors"
            />
          </div>
          <div v-if="forFinalService || completed">
            <Separator class="mb-3" />
            <InitialOnsiteDetails
              :is-editing="isEditing"
              is-clickable-file
              v-model:service-performed="form.initial_service_performed"
              v-model:machine-status="form.initial_machine_status"
              v-model:recommendation="form.recommendation"
              v-model:report-file="form.report_file"
              :errors="form.errors"
              :iTService="data.serviceable"
            />
          </div>
          <div v-if="completed">
            <Separator class="mb-3" />
            <FinalOnsiteDetails
              :is-editing="isEditing"
              v-model:service-performed="form.final_service_performed"
              v-model:parts-replaced="form.parts_replaced"
              v-model:remarks="form.remarks"
              v-model:machine-status="form.final_machine_status"
              :errors="form.errors"
            />
          </div>
          <div v-show="isEditing">
            <Separator class="mb-3" />
            <CorrectionReason
              v-model:reason="form.reason"
              :error="form.errors?.reason"
            />
          </div>
          <div
            v-if="isEditing"
            class="col-span-2 flex flex-row items-center justify-end gap-3"
          >
            <Button
              v-show="form.processing"
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
                v-show="form.processing"
                class="animate-spin"
              />
              Submit Report
            </Button>
          </div>
        </form>
      </div>
    </MainContainer>
  </AppLayout>
</template>
