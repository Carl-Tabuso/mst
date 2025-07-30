<script setup lang="ts">
import { Label } from '@/components/ui/label'
import { formatToDateString } from '@/composables/useDateFormatter'

interface FirstSectionViewProps {
  serviceType: string
  serviceDate: any
  serviceTime: string
  client: string
  address: string
  department: string
  contactPosition: string
  contactNumber: string
  contactPerson: string
}

defineProps<FirstSectionViewProps>()
</script>

<template>
  <div class="mx-6 grid grid-cols-2 gap-y-5 text-sm">
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Type of Service</Label>
      <div>{{ serviceType }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Date and Time of Service</Label>
      <div>
        {{ formatToDateString(serviceDate.toString()) }} at {{ serviceTime }}
      </div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Client</Label>
      <div>{{ client }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Address</Label>
      <div class="w-full">{{ address }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Department/Branch</Label>
      <div>{{ department }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Position</Label>
      <div>{{ contactPosition }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Contact Person</Label>
      <div>{{ contactPerson }}</div>
    </div>
    <div class="flex flex-col">
      <Label class="text-muted-foreground">Contact Number</Label>
      <div>{{ contactNumber }}</div>
    </div>
  </div>
</template>
