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

interface MachineDetailsProps {
  errors?: any
  isEditing?: boolean
}

withDefaults(defineProps<MachineDetailsProps>(), {
  isEditing: false,
})

const machineType = defineModel<string>('machineType')
const machineModel = defineModel<string>('machineModel')
const serialNumber = defineModel<string | number>('serialNumber')
const tagNumber = defineModel<string | number>('tagNumber')
const machineProblem = defineModel<string>('machineProblem')
</script>

<template>
  <div>
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Machine Details</div>
      <p class="text-sm text-muted-foreground">
        Machine specifications and description of current problem.
      </p>
    </div>
    <div
      class="grid grid-cols-1 gap-y-4 sm:grid-cols-[auto,1fr] sm:gap-x-7 sm:gap-y-3"
    >
      <div
        class="grid grid-cols-1 gap-y-4 sm:col-span-2 sm:grid-cols-2 sm:gap-x-10"
      >
        <div class="flex flex-col sm:flex-row sm:items-start sm:gap-x-4">
          <Label
            for="machineType"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Machine Type
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Select v-model="machineType">
              <SelectTrigger
                id="machineType"
                :disabled="!isEditing"
                :class="{
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors?.machine_type,
                }"
              >
                <SelectValue placeholder="Select a machine type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="(machineType, index) in [
                    'Printer',
                    'Laptop',
                    'Desktop',
                    'Server',
                    'Other',
                  ]"
                  :key="index"
                  :value="machineType"
                >
                  {{ machineType }}
                </SelectItem>
              </SelectContent>
            </Select>
            <InputError :message="errors?.machine_type" />
          </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="machineModel"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-36"
          >
            Model
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="machineModel"
              type="text"
              placeholder="Enter machine model"
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors?.model,
              }"
              v-model="machineModel"
            />
            <InputError :message="errors?.model" />
          </div>
        </div>
      </div>
      <div
        class="grid grid-cols-1 gap-y-4 sm:col-span-2 sm:grid-cols-2 sm:gap-x-10"
      >
        <div class="flex flex-col sm:flex-row sm:items-start sm:gap-x-4">
          <Label
            for="serialNumber"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-44"
          >
            Serial Number
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="serialNumber"
              type="text"
              placeholder="Enter machine serial number"
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors?.serial_no,
              }"
              v-model="serialNumber"
            />
            <InputError :message="errors?.serial_no" />
          </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-start">
          <Label
            for="tagNumber"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:mt-3 sm:w-36"
          >
            Tag Number
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Input
              id="tagNumber"
              type="text"
              placeholder="Enter machine tag number"
              :disabled="!isEditing"
              :class="{
                'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                  errors?.tag_no,
              }"
              v-model="tagNumber"
            />
            <InputError :message="errors?.tag_no" />
          </div>
        </div>
        <div class="flex flex-col sm:col-span-2 sm:flex-row sm:items-start">
          <Label
            for="problem"
            class="mb-1 w-full shrink-0 sm:mb-0 sm:w-48 sm:pt-1"
          >
            Machine Problem
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Textarea
              id="problem"
              placeholder="Describe the machine problem"
              :disabled="!isEditing"
              :class="[
                'w-full',
                {
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    errors?.machine_problem,
                },
              ]"
              v-model="machineProblem"
            />
            <InputError :message="errors?.machine_problem" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
