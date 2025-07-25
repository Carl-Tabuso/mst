<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatToDateString } from '@/composables/useDateFormatter'
import { usePermissions } from '@/composables/usePermissions'
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
  errors: any
  employees?: Employee[]
  isSubmitBtnDisabled?: boolean
}

interface ThirdSectionEmits {
  onSubmit: void
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
const { can } = usePermissions()

const isCurrentStage = computed(() => isSuccessfulProposal(props.status))

const canNextStage = computed(() => {
  return isCurrentStage.value && can('update:job_order')
})

const isDisabled = computed(() => !props.isEditing && !canNextStage.value)
</script>

<template>
  <FormAreaInfo
    :condition="isCurrentStage"
    class="mb-4"
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
        <div class="flex items-start gap-x-4">
          <Label
            for="paymentType"
            class="mt-3 w-44 shrink-0"
          >
            Type of Payment
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="paymentType"
              :disabled="isDisabled"
              placeholder="Enter client's payment type"
              v-model="paymentType"
              :class="[
                'w-full',
                {
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors.payment_type,
                },
              ]"
            />
            <InputError :message="errors.payment_type" />
          </div>
        </div>
        <div class="flex items-start">
          <Label
            for="bidBond"
            class="mt-3 w-36 shrink-0"
          >
            Bid Bond
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="bidBond"
              :disabled="isDisabled"
              placeholder="Enter job order's bid bond"
              v-model="bidBond"
              :class="[
                'w-full',
                {
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors.bid_bond,
                },
              ]"
            />
            <InputError :message="errors.bid_bond" />
          </div>
        </div>
      </div>

      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-start gap-x-4">
          <Label
            for="orNumber"
            class="mt-3 w-44 shrink-0"
          >
            OR Number
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="orNumber"
              :disabled="isDisabled"
              placeholder="Enter OR Number"
              v-model="orNumber"
              :class="[
                'w-full',
                {
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors.or_number,
                },
              ]"
            />
            <InputError :message="errors.or_number" />
          </div>
        </div>
        <div class="flex items-start">
          <Label
            for="paymentDate"
            class="mt-3 w-36 shrink-0"
          >
            Date of Payment
          </Label>
          <div class="flex w-full flex-col gap-1">
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
                    {
                      'text-muted-foreground': !paymentDate,
                      'border-destructive': errors.payment_date,
                    },
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
            <InputError :message="errors.payment_date" />
          </div>
        </div>
      </div>

      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-start gap-x-4">
          <Label
            for="approvedDate"
            class="mt-3 w-44 shrink-0"
          >
            Date Approved
          </Label>
          <div class="flex w-full flex-col gap-1">
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
                    {
                      'text-muted-foreground': !approvedDate,
                      'border-destructive': errors.approved_date,
                    },
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
            <InputError :message="errors.approved_date" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="canNextStage"
    class="flex justify-end"
  >
    <SectionButton
      :is-submit-btn-disabled="isSubmitBtnDisabled"
      @on-submit="$emit('onSubmit')"
      @on-cancel-submit="$emit('onCancelSubmit')"
    />
  </div>
</template>
