<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useCorrections } from '@/composables/useCorrections'
import { JobOrder } from '@/types'
import { Calendar } from 'lucide-vue-next'

interface SecondSectionProps {
  jobOrder: JobOrder
  changes: any
}

const { changes, jobOrder } = defineProps<SecondSectionProps>()

const { getChangedOrCurrentValue } = useCorrections(changes, jobOrder)
</script>

<template>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6 text-foreground">
        Proposal Information
      </div>
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
              v-bind="getChangedOrCurrentValue('payment_type')"
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
              v-bind="getChangedOrCurrentValue('bid_bond')"
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
              v-bind="getChangedOrCurrentValue('or_number')"
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
              v-bind="getChangedOrCurrentValue('payment_date')"
              class="pointer-events-none w-full ps-3 text-start font-normal"
            >
              {{ getChangedOrCurrentValue('payment_date').defaultValue }}
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
              v-bind="getChangedOrCurrentValue('approved_date')"
              class="pointer-events-none w-full ps-3 text-start font-normal"
            >
              {{ getChangedOrCurrentValue('approved_date').defaultValue }}
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
