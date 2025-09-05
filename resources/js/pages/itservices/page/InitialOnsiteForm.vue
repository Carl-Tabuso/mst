<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue'
import { BreadcrumbItem, ITService, JobOrder } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import InitialOnsiteDetails from '../components/InitialOnsiteDetails.vue'

interface InitialOnsiteFormProps {
  iTService: ITService & { jobOrder: JobOrder }
}

const props = defineProps<InitialOnsiteFormProps>()

const form = useForm({
  service_performed: '',
  recommendation: '',
  machine_status: '',
  report_file: null as any,
})

const onSubmit = () => {
  form.post(
    route('job_order.it_service.onsite.initial.store', props.iTService.id),
    {
      onStart: () => form.clearErrors(),
      forceFormData: true,
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
    title: props.iTService.jobOrder.ticket,
    href: '#',
  },
]
</script>

<template>
  <Head :title="iTService.jobOrder.ticket" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
      <div class="border-b border-border bg-background shadow-sm">
        <div class="mb-3 flex flex-row items-center justify-between">
          <TicketHeader :job-order="iTService.jobOrder" />
        </div>
      </div>
      <form
        @submit.prevent="onSubmit"
        enctype="multipart/form-data"
        class="mt-4"
      >
        <InitialOnsiteDetails
          is-editing
          v-model:service-performed="form.service_performed"
          v-model:recommendation="form.recommendation"
          v-model:machine-status="form.machine_status"
          v-model:report-file="form.report_file"
          :errors="form.errors"
        />
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
    </MainContainer>
  </AppLayout>
</template>
