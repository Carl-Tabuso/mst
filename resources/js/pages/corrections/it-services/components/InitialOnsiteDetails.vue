<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useCorrections } from '@/composables/useCorrections'
import { ITService, JobOrderCorrection } from '@/types'

interface InitialOnsiteDetailsProps {
  changes: any
  correction: JobOrderCorrection
}

const { changes, correction } = defineProps<InitialOnsiteDetailsProps>()

const { getChangedOrCurrentValue } = useCorrections(
  changes,
  correction.jobOrder,
)

const reportFile = getChangedOrCurrentValue('file_name')

const fileViewingUrl = reportFile.hasOwnProperty('class')
  ? route('job_order.correction.initial_onsite_report_tempfile', correction.id)
  : route('job_order.it_service.onsite.initial.show_file', {
      iTService: correction.jobOrder.serviceable.id,
      initialOnsite: (correction.jobOrder.serviceable as ITService)!
        .initialOnsiteReport!.id,
    })
</script>

<template>
  <div>
    <div class="mb-6">
      <div>
        <div class="text-xl font-semibold leading-6 text-foreground">
          First Onsite Visit
        </div>
        <p class="text-sm text-muted-foreground">
          The initial onsite report details.
        </p>
      </div>
    </div>
    <div class="mt-4 grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
      <div class="col-span-2 flex flex-row items-start gap-x-4">
        <Label class="mt-3 w-44 shrink-0"> Services Performed </Label>
        <Textarea
          v-bind="getChangedOrCurrentValue('initial_service_performed')"
          class="pointer-events-none"
        />
      </div>
      <div class="col-span-2 flex flex-row items-start gap-x-4">
        <Label class="mt-3 w-44 shrink-0"> Recommendation </Label>
        <Textarea
          v-bind="getChangedOrCurrentValue('recommendation')"
          class="pointer-events-none"
        />
      </div>
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex flex-row items-center gap-x-4">
          <Label class="w-44 shrink-0"> Report File </Label>
          <div
            class="flex h-10 w-full min-w-0 flex-1 flex-row items-center gap-1 rounded-md border px-3 py-2"
            :class="reportFile.class"
          >
            <a
              :href="fileViewingUrl"
              target="_blank"
              class="truncate text-sm text-foreground underline"
            >
              {{ reportFile.defaultValue }}
            </a>
          </div>
        </div>
        <div class="flex flex-row items-center">
          <Label class="w-36 shrink-0"> Machine Status </Label>
          <Input
            v-bind="getChangedOrCurrentValue('initial_machine_status')"
            class="pointer-events-none"
          />
        </div>
      </div>
    </div>
  </div>
</template>
