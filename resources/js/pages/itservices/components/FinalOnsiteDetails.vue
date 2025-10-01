<script setup lang="ts">
import InputError from '@/components/InputError.vue'
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

interface FinalOnsiteFormProps {
  errors: any
  isEditing?: boolean
}

withDefaults(defineProps<FinalOnsiteFormProps>(), {
  isEditing: false,
})

const servicePerformed = defineModel<string>('servicePerformed')
const partsReplaced = defineModel<string>('partsReplaced')
const remarks = defineModel<string>('remarks')
const machineStatus = defineModel<string>('machineStatus')
</script>

<template>
  <div>
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
    <div
      class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-[auto,1fr] sm:gap-x-12"
    >
      <div class="col-span-1 sm:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="servicePerformed"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Service Performed
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="servicePerformed"
              placeholder="Describe all services performed during this final visit."
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors.service_performed,
              }"
              v-model="servicePerformed"
            />
            <InputError :message="errors.service_performed" />
          </div>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="partsReplaced"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Parts Replaced
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="partsReplaced"
              placeholder="List all the parts that were replaced or installed."
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors.parts_replaced,
              }"
              v-model="partsReplaced"
            />
            <InputError :message="errors.parts_replaced" />
          </div>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="remarks"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Remarks
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="remarks"
              placeholder="Provide the final remarks, current machine status, and any follow-up recommendations."
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors.remarks,
              }"
              v-model="remarks"
            />
            <InputError :message="errors.remarks" />
          </div>
        </div>
      </div>
      <div class="col-span-1 sm:col-span-2">
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="machineStatus"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Machine Status
          </Label>
          <div class="flex w-full flex-col gap-1 sm:w-1/2">
            <Select v-model="machineStatus">
              <SelectTrigger
                id="machineStatus"
                :disabled="!isEditing"
                :class="{
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors.machine_status,
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
            <InputError :message="errors.machine_status" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
