<script setup lang="ts">
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue'
import { BreadcrumbItem, ITService, JobOrder } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import FinalOnsiteDetails from '../components/FinalOnsiteDetails.vue'

interface FinalOnsiteFormProps {
  service: ITService & { jobOrder: JobOrder }
}

const props = defineProps<FinalOnsiteFormProps>()

const form = useForm({
  service_performed: '',
  parts_replaced: '',
  remarks: '',
  machine_status: '',
})

const onSubmit = () => {
  form.post(
    route('job_order.it_service.onsite.final.store', props.service.id),
    {
      onStart: () => form.clearErrors(),
      preserveState: true,
      replace: true,
    },
  )
}

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
    title: props.service.jobOrder.ticket,
    href: '#',
  },
]
</script>

<template>
  <Head :title="service.jobOrder.ticket" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="border-b border-border bg-background shadow-sm">
        <div class="mb-3 flex flex-row items-center justify-between">
          <TicketHeader :job-order="service.jobOrder" />
        </div>
      </div>
      <form
        @submit.prevent="onSubmit"
        class="mt-4"
      >
        <FinalOnsiteDetails
          is-editing
          v-model:service-performed="form.service_performed"
          v-model:parts-replaced="form.parts_replaced"
          v-model:remarks="form.remarks"
          v-model:machine-status="form.machine_status"
          :errors="form.errors"
        />
        <div class="col-span-2 flex flex-row items-center justify-end gap-3">
          <Button
            type="submit"
            :disabled="form.processing"
          >
            <LoaderCircle
              v-if="form.processing"
              class="animate-spin"
            />
            Submit Final Report
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
