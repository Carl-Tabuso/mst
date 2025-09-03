<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { machineStatuses } from '@/constants/machine-statuses'
import AppLayout from '@/layouts/AppLayout.vue'
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue'
import { BreadcrumbItem, ITService, JobOrder } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

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
      <div class="mt-4">
        <div class="mb-6">
          <div>
            <div class="text-xl font-semibold leading-6 text-foreground">
              Final Onsite Visit
            </div>
            <p class="text-sm text-muted-foreground">
              The final onsite report details.
            </p>
          </div>
        </div>
      </div>
      <form
        @submit.prevent="onSubmit"
        class="mt-4 grid grid-cols-[auto,1fr] gap-x-12 gap-y-6"
      >
        <div class="col-span-2 flex flex-row items-start">
          <Label
            for="servicePerformed"
            class="mt-3 w-44 shrink-0"
          >
            Service Performed
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="servicePerformed"
              placeholder="Describe all services performed during this final visit."
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  form.errors.service_performed,
              }"
              v-model="form.service_performed"
            />
            <InputError :message="form.errors.service_performed" />
          </div>
        </div>
        <div class="col-span-2 flex flex-row items-start">
          <Label
            for="partsReplaced"
            class="mt-3 w-44 shrink-0"
          >
            Parts Replaced
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="partsReplaced"
              placeholder="List all the parts that were replaced or installed."
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  form.errors.parts_replaced,
              }"
              v-model="form.parts_replaced"
            />
            <InputError :message="form.errors.parts_replaced" />
          </div>
        </div>
        <div class="col-span-2 flex flex-row items-start">
          <Label
            for="remarks"
            class="mt-3 w-44 shrink-0"
          >
            Remarks
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="remarks"
              placeholder="Provide the final remarks, current machine status, and any follow-up recommendations."
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  form.errors.remarks,
              }"
              v-model="form.remarks"
            />
            <InputError :message="form.errors.remarks" />
          </div>
        </div>
        <div class="col-span-2 flex flex-row items-start">
          <Label
            for="machineStatus"
            class="mt-3 w-44 shrink-0"
          >
            Machine Status
          </Label>
          <div class="flex w-[50%] flex-col gap-1">
            <Select v-model="form.machine_status">
              <SelectTrigger
                id="machineStatus"
                :class="{
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    form.errors.machine_status,
                }"
              >
                <SelectValue placeholder="Select machine status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="{ id, label } in machineStatuses"
                  :key="id"
                  :value="id"
                >
                  {{ label }}
                </SelectItem>
              </SelectContent>
            </Select>
            <InputError :message="form.errors.machine_status" />
          </div>
        </div>
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
