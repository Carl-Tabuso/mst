<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Textarea } from '@/components/ui/textarea'
import UserAvatar from '@/components/UserAvatar.vue'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { format } from 'date-fns'
import { Calendar, Check, ChevronsUpDown } from 'lucide-vue-next'

interface FirstSectionProps {
  isEditing?: boolean
  isServiceTypeDisabled?: boolean
  errors?: any
  technicians?: Employee[]
}

withDefaults(defineProps<FirstSectionProps>(), {
  isEditing: false,
  isServiceTypeDisabled: false,
})

const serviceType = defineModel<string>('serviceType')
const serviceDate = defineModel<any>('serviceDate', {
  get(value) {
    if (value) {
      const formatted = format(value, 'yyyy-MM-dd')
      return parseDate(formatted)
    }
  },
  set(value) {
    return new Date(value).toISOString()
  },
})
const serviceTime = defineModel<string>('serviceTime')
const client = defineModel<string>('client')
const address = defineModel<string>('address')
const department = defineModel<string>('department')
const contactPosition = defineModel<string>('contactPosition')
const contactNumber = defineModel<string>('contactNumber')
const contactPerson = defineModel<string>('contactPerson')
const technician = defineModel<Employee | null>('technician')
const description = defineModel<string>('description', {
  default: '',
})
</script>

<template>
  <div>
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Job Order Details</div>
      <p class="text-sm text-muted-foreground">
        General information of the requested service and client information.
      </p>
    </div>
    <div
      class="grid grid-cols-1 gap-y-5 md:grid-cols-[auto,1fr] md:gap-x-7 md:gap-y-3"
    >
      <Label class="self-start md:self-center">Type of Service</Label>
      <RadioGroup
        required
        v-model="serviceType"
        :disabled="isServiceTypeDisabled"
        class="flex flex-col gap-y-3 md:flex-row md:items-center md:gap-x-10"
      >
        <div class="flex items-center gap-x-2">
          <RadioGroupItem
            id="wm"
            value="form4"
          />
          <Label for="wm">Waste Management</Label>
        </div>
        <div class="flex items-center gap-x-2">
          <RadioGroupItem
            id="its"
            value="it_service"
          />
          <Label for="its">IT Services</Label>
        </div>
        <div class="flex items-center gap-x-2">
          <RadioGroupItem
            id="os"
            value="form5"
          />
          <Label for="os">Other Services (specify)</Label>
        </div>
      </RadioGroup>
      <Label class="self-start md:self-center">Date and Time of Service</Label>
      <div class="flex flex-col gap-y-3 md:flex-row md:items-center md:gap-x-4">
        <Popover>
          <PopoverTrigger
            as-child
            :disabled="!isEditing"
          >
            <Button
              type="button"
              variant="outline"
              class="w-full ps-3 text-start font-normal md:w-[400px]"
              :class="{ 'border-destructive': errors?.date_time }"
            >
              <span>{{ format(serviceDate.toString(), 'MMMM d, yyyy') }}</span>
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <AppCalendar
              :model-value="serviceDate"
              @update:model-value="(value) => (serviceDate = value)"
            />
          </PopoverContent>
        </Popover>
        <Input
          id="time"
          type="time"
          v-model="serviceTime"
          required
          :disabled="!isEditing"
          class="w-full appearance-none bg-background md:w-[120px] [&::-webkit-calendar-picker-indicator]:hidden"
          :class="{ 'border-destructive': errors?.date_time }"
          placeholder="Select a time"
        />
      </div>
      <Label
        for="client"
        class="self-start md:self-center"
        >Client</Label
      >
      <div class="flex flex-col">
        <Input
          id="client"
          type="text"
          required
          :disabled="!isEditing"
          placeholder="Enter client/company name"
          v-model="client"
          class="w-full md:w-[515px]"
          :class="{
            'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
              errors?.client,
          }"
        />
        <InputError :message="errors?.client" />
      </div>
      <Label
        for="address"
        class="self-start pt-1"
        >Address</Label
      >
      <Textarea
        id="address"
        required
        :disabled="!isEditing"
        placeholder="Enter client's complete address"
        v-model="address"
        class="w-full"
      />
      <Label
        for="description"
        class="self-start pt-1"
      >
        Description
      </Label>
      <Textarea
        id="description"
        :disabled="!isEditing"
        placeholder="Enter ticket's description"
        v-model="description"
        class="w-full"
      />
      <div
        class="col-span-1 flex flex-col gap-y-3 md:col-span-2 md:grid md:grid-cols-2 md:gap-x-10"
      >
        <div
          class="flex flex-col gap-y-2 md:flex-row md:items-center md:gap-x-4"
        >
          <Label
            for="department"
            class="shrink-0 md:w-44"
            >Department/Branch</Label
          >
          <Input
            id="department"
            type="text"
            required
            :disabled="!isEditing"
            placeholder="Enter client/company's department"
            v-model="department"
            class="w-full md:w-[400px]"
          />
        </div>
        <div class="flex flex-col gap-y-2 md:flex-row md:items-center">
          <Label
            for="position"
            class="shrink-0 md:w-36"
            >Contact Position</Label
          >
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
      <div
        class="col-span-1 flex flex-col gap-y-3 md:col-span-2 md:grid md:grid-cols-2 md:gap-x-10"
      >
        <div
          class="flex flex-col gap-y-2 md:flex-row md:items-center md:gap-x-4"
        >
          <Label
            for="contactPerson"
            class="shrink-0 md:w-44"
            >Contact Person</Label
          >
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
        <div class="flex flex-col gap-y-2 md:flex-row md:items-center">
          <Label
            for="contactNumber"
            class="shrink-0 md:w-36"
            >Contact Number</Label
          >
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
      <div
        v-if="serviceType === 'it_service'"
        class="col-span-1 flex flex-col gap-y-3 md:col-span-2 md:grid md:grid-cols-2 md:gap-x-10"
      >
        <div
          class="flex flex-col gap-y-2 md:flex-row md:items-center md:gap-x-4"
        >
          <Label class="shrink-0 md:w-44">Technician</Label>
          <div class="flex w-full flex-col">
            <Popover>
              <PopoverTrigger
                as-child
                :disabled="!isEditing"
                :class="{
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors?.technician_id,
                }"
              >
                <Button
                  variant="outline"
                  class="w-full"
                >
                  <template v-if="technician">
                    <UserAvatar
                      :avatar-path="technician?.account?.avatar"
                      :fallback="technician.fullName"
                    />
                    <span
                      class="flex items-center justify-between gap-2 rounded-md text-xs"
                    >
                      {{ technician.fullName }}
                    </span>
                  </template>
                  <span
                    v-else
                    class="font-normal text-muted-foreground"
                  >
                    Select a technician
                  </span>
                  <ChevronsUpDown class="ml-auto h-4 w-4" />
                </Button>
              </PopoverTrigger>
              <PopoverContent
                class="w-full p-0"
                align="start"
              >
                <Command>
                  <CommandInput placeholder="Search a technician" />
                  <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup>
                      <CommandItem
                        v-for="availableTechnician in technicians"
                        :key="availableTechnician.id"
                        :value="availableTechnician"
                        class="cursor-pointer"
                        @select="technician = availableTechnician"
                      >
                        <UserAvatar
                          :avatar-path="availableTechnician?.account?.avatar"
                          :fallback="availableTechnician.fullName"
                        />
                        <div
                          class="grid flex-1 text-left text-[13px] leading-tight"
                        >
                          <span class="truncate">{{
                            availableTechnician.fullName
                          }}</span>
                        </div>
                        <Check
                          :class="[
                            'ml-auto h-4 w-4',
                            availableTechnician === technician
                              ? 'opacity-100'
                              : 'opacity-0',
                          ]"
                        />
                      </CommandItem>
                    </CommandGroup>
                  </CommandList>
                </Command>
              </PopoverContent>
            </Popover>
            <InputError :message="errors?.technician_id" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
