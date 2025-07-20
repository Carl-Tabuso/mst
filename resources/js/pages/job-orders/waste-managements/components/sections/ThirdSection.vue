<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatToDateString } from '@/composables/useDateFormatter'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'
import FormAreaInfo from '../FormAreaInfo.vue'
import SectionButton from '../SectionButton.vue'

interface ThirdSectionProps {
  isEditing?: boolean
  status: JobOrderStatus
  employees?: Employee[]
  isSubmitBtnDisabled?: boolean
}

interface ThirdSectionEmits {
  onCancelSubmit: void
}

const props = withDefaults(defineProps<ThirdSectionProps>(), {
  isEditing: false,
  isSubmitBtnDisabled: false,
})

defineEmits<ThirdSectionEmits>()

const paymentType = defineModel<string>('paymentType')
const bidBond = defineModel<string | number>('bidBond')
const orNumber = defineModel<string>('orNumber')
const paymentDate = defineModel<any>('paymentDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})
const approvedDate = defineModel<any>('approvedDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})

const handlePaymentDateChange = (value: any) => {
  paymentDate.value = new Date(value).toISOString()
}

const handleApprovedDateChange = (value: any) => {
  approvedDate.value = new Date(value).toISOString()
}

const { isSuccessfulProposal } = useWasteManagementStages()

const canNextStage = computed(() => isSuccessfulProposal(props.status))

const isDisabled = computed(() => !props.isEditing && !canNextStage.value)
</script>

<template>
  <FormAreaInfo
    :condition="canNextStage"
    class="mb-2"
  >
    <span class="px-1 font-semibold">Frontliner </span>
    is required to complete this section if the proposal status was
    <span class="px-1 font-semibold">Successful, </span>
    to continue with hauling.
  </FormAreaInfo>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6">Proposal Information</div>
      <p class="text-sm text-muted-foreground">
        Information regarding business client's payment.
      </p>
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-24 gap-y-3">
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-center gap-x-4">
          <Label
            for="paymentType"
            class="w-44 shrink-0"
          >
            Type of Payment
          </Label>
          <Input
            id="paymentType"
            :disabled="isDisabled"
            placeholder="Enter client's payment type"
            v-model="paymentType"
            class="w-full"
          />
        </div>
        <div class="flex items-center">
          <Label
            for="bidBond"
            class="w-36 shrink-0"
          >
            Bid Bond
          </Label>
          <Input
            id="bidBond"
            :disabled="isDisabled"
            placeholder="Enter job order's bid bond"
            v-model="bidBond"
            class="w-full"
          />
        </div>
      </div>

      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-center gap-x-4">
          <Label
            for="orNumber"
            class="w-44 shrink-0"
          >
            OR Number
          </Label>
          <Input
            id="orNumber"
            :disabled="isDisabled"
            placeholder="Enter OR Number"
            v-model="orNumber"
            class="w-full"
          />
        </div>
        <div class="flex items-center">
          <Label
            for="paymentDate"
            class="w-36 shrink-0"
          >
            Date of Payment
          </Label>
          <Popover>
            <PopoverTrigger
              as-child
              :disabled="isDisabled"
            >
              <Button
                type="button"
                variant="outline"
                :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !paymentDate },
                ]"
              >
                <span>
                  {{
                    paymentDate
                      ? formatToDateString(paymentDate.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :model-value="paymentDate"
                @update:model-value="handlePaymentDateChange"
              />
            </PopoverContent>
          </Popover>
        </div>
      </div>

      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-center gap-x-4">
          <Label
            for="approvedDate"
            class="w-44 shrink-0"
          >
            Date Approved
          </Label>
          <Popover>
            <PopoverTrigger
              as-child
              :disabled="isDisabled"
            >
              <Button
                type="button"
                variant="outline"
                :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !approvedDate },
                ]"
              >
                <span>
                  {{
                    approvedDate
                      ? formatToDateString(approvedDate.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :model-value="approvedDate"
                @update:model-value="handleApprovedDateChange"
              />
            </PopoverContent>
          </Popover>
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="canNextStage"
    class="mt-6 flex justify-end space-x-2"
  >
    <SectionButton
      :is-submit-btn-disabled="isSubmitBtnDisabled"
      @on-cancel-submit="$emit('onCancelSubmit')"
    />
  </div>
</template>
