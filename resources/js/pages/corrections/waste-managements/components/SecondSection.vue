<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useCorrections } from '@/composables/useCorrections'
import { JobOrder } from '@/types'
import { format } from 'date-fns'
import { Calendar } from 'lucide-vue-next'

interface SecondSectionProps {
  jobOrder: JobOrder
  changes: any
}

const props = defineProps<SecondSectionProps>()

const { canCorrectProposalInformation, fieldMap, getNestedObject } =
  useCorrections()

const formatDateToMDY = (date: Date | string) => {
  const d = typeof date === 'string' ? new Date(date) : date
  return format(d, 'MMMM d, yyyy')
}

const fieldFormatters: Partial<
  Record<keyof typeof fieldMap, (val: any) => string>
> = {
  payment_date: formatDateToMDY,
  approved_date: formatDateToMDY,
}

const changedFields = Object.keys(props.changes.after)

const wasChanged = (field: keyof typeof fieldMap) => {
  const formatter = fieldFormatters[field]

  if (changedFields.includes(field)) {
    const value = props.changes.after[field]
    return {
      defaultValue: formatter ? formatter(value) : value,
      class: 'bg-amber-50 border-warning dark:bg-transparent',
    }
  } else {
    const value = getNestedObject(props.jobOrder, fieldMap[field])
    return {
      defaultValue: formatter && value ? formatter(value) : value,
    }
  }
}
</script>

<template>
  <div
    v-if="canCorrectProposalInformation(props.jobOrder.status)"
    class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6"
  >
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
              v-bind="wasChanged('payment_type')"
              class="pointer-events-none w-full"
            />
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
              v-bind="wasChanged('bid_bond')"
              class="pointer-events-none w-full"
            />
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
              v-bind="wasChanged('or_number')"
              class="pointer-events-none w-full"
            />
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
            <Button
              type="button"
              variant="outline"
              v-bind="wasChanged('payment_date')"
              class="pointer-events-none w-full ps-3 text-start font-normal"
            >
              {{ wasChanged('payment_date').defaultValue }}
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
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
            <Button
              type="button"
              variant="outline"
              v-bind="wasChanged('approved_date')"
              class="pointer-events-none w-full ps-3 text-start font-normal"
            >
              {{ wasChanged('approved_date').defaultValue }}
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
