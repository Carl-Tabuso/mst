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
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'
import { formatToDateString } from '@/composables/useDateFormatter'
import { parseDate } from '@internationalized/date'
import { Calendar } from 'lucide-vue-next'

interface FirstSectionProps {
  isEditing: boolean
  isServiceTypeDisabled?: boolean
  errors?: any
}

withDefaults(defineProps<FirstSectionProps>(), {
  isEditing: false,
  isServiceTypeDisabled: false,
})

const serviceType = defineModel<string>('serviceType')
const serviceDate = defineModel<any>('serviceDate', {
  get(value) {
    return parseDate(value.split('T')[0])
  },
})
const serviceTime = defineModel<string>('serviceTime')
const client = defineModel<string>('client')
const address = defineModel<string>('address')
const department = defineModel<string>('department')
const contactPosition = defineModel<string>('contactPosition')
const contactNumber = defineModel<string>('contactNumber')
const contactPerson = defineModel<string>('contactPerson')

const handleDateOfServiceChange = (value: any) => {
  serviceDate.value = new Date(value).toISOString()
}
</script>

<template>
  <div class="mb-6">
    <div class="text-xl font-semibold leading-6">Job Order Details</div>
    <p class="text-sm text-muted-foreground">
      General information of the requested service and client information.
    </p>
  </div>
  <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
    <Label class="self-center"> Type of Service </Label>
    <RadioGroup
      required
      v-model="serviceType"
      :disabled="isServiceTypeDisabled"
      class="flex items-center gap-x-10"
    >
      <div class="flex items-center gap-x-2">
        <RadioGroupItem
          id="wm"
          value="form4"
        />
        <Label for="wm"> Waste Management </Label>
      </div>
      <div class="flex items-center gap-x-2">
        <RadioGroupItem
          id="its"
          value="it_service"
        />
        <Label for="its"> IT Services </Label>
      </div>
      <div class="flex items-center gap-x-2">
        <RadioGroupItem
          id="os"
          value="form5"
        />
        <Label for="os"> Other Services (specify) </Label>
      </div>
    </RadioGroup>
    <Label class="self-center"> Date and Time of Service </Label>
    <div class="flex items-center gap-x-4">
      <Popover>
        <PopoverTrigger
          as-child
          :disabled="!isEditing"
        >
          <Button
            type="button"
            variant="outline"
            :class="[
              'w-[400px] ps-3 text-start font-normal',
              { 'border-destructive': errors?.date_time },
            ]"
          >
            <span>
              {{ formatToDateString(serviceDate.toString()) }}
            </span>
            <Calendar class="ms-auto h-4 w-4 opacity-50" />
          </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
          <AppCalendar
            :model-value="serviceDate"
            @update:model-value="handleDateOfServiceChange"
          />
        </PopoverContent>
      </Popover>
      <Input
        id="time"
        type="time"
        v-model="serviceTime"
        required
        :disabled="!isEditing"
        :class="[
          'w-[100px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden',
          { 'border-destructive': errors?.date_time },
        ]"
        placeholder="Select a time"
      />
    </div>
    <Label
      for="client"
      class="self-center"
    >
      Client
    </Label>
    <div class="flex flex-col">
      <Input
        id="client"
        type="text"
        required
        :disabled="!isEditing"
        placeholder="Enter client/company name"
        v-model="client"
        :class="[
          'w-[515px]',
          {
            'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
              errors?.client,
          },
        ]"
      />
      <InputError :message="errors?.client" />
    </div>
    <Label
      for="address"
      class="self-start pt-1"
    >
      Address
    </Label>
    <Textarea
      id="address"
      required
      :disabled="!isEditing"
      placeholder="Enter client's complete address"
      v-model="address"
      class="w-full"
    />
    <div class="col-span-2 grid grid-cols-2 gap-x-10">
      <div class="flex items-center gap-x-4">
        <Label
          for="department"
          class="w-44 shrink-0"
        >
          Department/Branch
        </Label>
        <Input
          id="department"
          type="text"
          required
          :disabled="!isEditing"
          placeholder="Enter client/company's department"
          v-model="department"
          class="w-[400px]"
        />
      </div>
      <div class="flex items-center">
        <Label
          for="position"
          class="w-36 shrink-0"
        >
          Contact Position
        </Label>
        <Input
          id="position"
          type="text"
          required
          :disabled="!isEditing"
          placeholder="Enter contact's position"
          v-model="contactPosition"
          class="w-full"
        />
      </div>
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-10">
      <div class="flex items-center gap-x-4">
        <Label
          for="contactPerson"
          class="w-44 shrink-0"
        >
          Contact Person
        </Label>
        <Input
          id="contactPerson"
          type="text"
          required
          :disabled="!isEditing"
          placeholder="Enter contact person"
          v-model="contactPerson"
          class="w-full"
        />
      </div>
      <div class="flex items-center">
        <Label
          for="contactNumber"
          class="w-36 shrink-0"
        >
          Contact Number
        </Label>
        <Input
          id="contactNumber"
          type="text"
          minlength="11"
          maxlength="11"
          required
          :disabled="!isEditing"
          placeholder="Enter contact person number"
          v-model="contactNumber"
          class="w-full"
        />
      </div>
    </div>
  </div>
</template>
