<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'
import { useCorrections } from '@/composables/useCorrections'
import { JobOrder } from '@/types'
import { format } from 'date-fns'
import { Calendar } from 'lucide-vue-next'

interface FirstSectionProps {
  jobOrder: JobOrder
  changes: any
}

const props = defineProps<FirstSectionProps>()

const { getNestedObject, fieldMap } = useCorrections()

const fieldFormatters: Partial<
  Record<keyof typeof fieldMap, (val: any) => string>
> = {
  date_time: (val) => {
    const d = typeof val === 'string' ? new Date(val) : val
    return {
      date: format(d, 'MMMM d, yyyy'),
      time: format(d, 'HH:mm'),
    }
  },
}

const changedFields = Object.keys(props.changes.after)

const wasChanged = (field: keyof typeof fieldMap) => {
  const formatter = fieldFormatters[field]

  if (changedFields.includes(field)) {
    const value = props.changes.after[field]
    if (field === 'date_time' && formatter) {
      return {
        defaultValue: formatter(value),
        class: 'bg-amber-50 border-warning dark:bg-transparent',
      }
    }
    return {
      defaultValue: formatter ? formatter(value) : value,
      class: 'bg-amber-50 border-warning dark:bg-transparent',
    }
  } else {
    const value = getNestedObject(props.jobOrder, fieldMap[field].path)
    if (field === 'date_time' && formatter) {
      return {
        defaultValue: formatter(value),
      }
    }
    return {
      defaultValue: formatter && value ? formatter(value) : value,
    }
  }
}
</script>

<template>
  <div class="my-4 flex flex-col gap-4 rounded-xl">
    <div class="mb-3 flex items-center">
      <div class="flex w-full flex-col">
        <div class="grid gap-y-6">
          <div>
            <h4 class="text-xl font-semibold leading-6 text-foreground">
              Job Order Details
            </h4>
            <p class="text-sm text-muted-foreground">
              General information of the requested service and client
              information.
            </p>
          </div>
          <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
            <Label class="self-center"> Type of Service </Label>
            <RadioGroup
              required
              v-model="jobOrder.serviceableType"
              class="pointer-events-none flex items-center gap-x-10"
            >
              <div class="flex items-center gap-x-2">
                <RadioGroupItem
                  id="wm"
                  value="form4"
                />
                <Label> Waste Management </Label>
              </div>
              <div class="flex items-center gap-x-2">
                <RadioGroupItem
                  id="its"
                  value="it_service"
                />
                <Label> IT Services </Label>
              </div>
              <div class="flex items-center gap-x-2">
                <RadioGroupItem
                  id="os"
                  value="form5"
                  checked
                />
                <Label> Other Services (specify) </Label>
              </div>
            </RadioGroup>
            <Label class="self-center"> Date and Time of Service </Label>
            <div class="flex items-center gap-x-4">
              <Button
                type="button"
                variant="outline"
                v-bind="wasChanged('date_time')"
                class="pointer-events-none w-[400px] ps-3 text-start font-normal"
              >
                <span>
                  {{ wasChanged('date_time').defaultValue.date }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
              <Input
                id="time"
                type="time"
                :default-value="wasChanged('date_time').defaultValue.time"
                :class="wasChanged('date_time').class"
                class="pointer-events-none w-[100px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden"
              />
            </div>
            <Label class="self-center"> Client </Label>
            <Input
              id="client"
              type="text"
              v-bind="wasChanged('client')"
              class="pointer-events-none w-[515px]"
            />
            <Label class="self-start pt-1"> Address </Label>
            <Textarea
              id="address"
              class="pointer-events-none w-full"
              v-bind="wasChanged('address')"
            />
            <div class="col-span-2 grid grid-cols-2 gap-x-10">
              <div class="flex items-center gap-x-4">
                <Label class="w-44 shrink-0"> Department/Branch </Label>
                <Input
                  id="department"
                  type="text"
                  class="pointer-events-none w-[400px]"
                  v-bind="wasChanged('department')"
                />
              </div>
              <div class="flex items-center">
                <Label class="w-36 shrink-0"> Contact Position </Label>
                <Input
                  id="position"
                  type="text"
                  class="pointer-events-none w-full"
                  v-bind="wasChanged('contact_position')"
                />
              </div>
            </div>
            <div class="col-span-2 grid grid-cols-2 gap-x-10">
              <div class="flex items-center gap-x-4">
                <Label class="w-44 shrink-0"> Contact Person </Label>
                <Input
                  id="contactPerson"
                  type="text"
                  class="pointer-events-none w-full"
                  v-bind="wasChanged('contact_person')"
                />
              </div>
              <div class="flex items-center">
                <Label class="w-36 shrink-0"> Contact Number </Label>
                <Input
                  id="contactNumber"
                  type="text"
                  v-bind="wasChanged('contact_no')"
                  class="pointer-events-none w-full"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>