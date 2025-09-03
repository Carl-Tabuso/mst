<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Input } from '@/components/ui/input'
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

interface InitialOnsiteFormProps {
  errors: any
}

defineProps<InitialOnsiteFormProps>()

const servicePerformed = defineModel<string>('servicePerformed')
const recommendation = defineModel<string>('recommendation')
const machineStatus = defineModel<string>('machineStatus')
const reportFile = defineModel<any>('reportFile')
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
      <div class="col-span-2">
        <div class="flex items-start">
          <Label
            for="servicesPerformed"
            class="mt-3 w-44 shrink-0"
          >
            Services Performed
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="servicesPerformed"
              v-model="servicePerformed"
              placeholder="Describe the services performed in detail"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors.service_performed,
              }"
            />
            <InputError :message="errors.service_performed" />
          </div>
        </div>
      </div>
      <div class="col-span-2 flex flex-row items-start">
        <Label
          for="recommendation"
          class="mt-3 w-44 shrink-0"
        >
          Recommendation
        </Label>
        <div class="flex w-full flex-col gap-1">
          <Textarea
            id="recommendation"
            v-model="recommendation"
            placeholder="Provide technician's recommendation"
            :class="{
              'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                errors.recommendation,
            }"
          />
          <InputError :message="errors.recommendation" />
        </div>
      </div>
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex flex-row items-start">
          <Label
            for="reportFile"
            class="mt-3 w-44 shrink-0"
          >
            Report File
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              type="file"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors.report_file,
              }"
              @input="reportFile = $event.target.files[0]"
            />
            <template v-if="errors.report_file">
              <InputError :message="errors.report_file" />
            </template>
            <p
              v-else
              class="text-xs text-muted-foreground"
            >
              Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)
            </p>
          </div>
        </div>
        <div class="flex flex-row items-start">
          <Label
            for="machineStatus"
            class="mt-3 w-36 shrink-0"
          >
            Machine Status
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Select v-model="machineStatus">
              <SelectTrigger
                id="machineStatus"
                :class="{
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors.machine_status,
                }"
              >
                <SelectValue placeholder="Select current machine status" />
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
            <InputError :message="errors.machine_status" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
