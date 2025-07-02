<script setup lang="ts">
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { PropType } from 'vue'
import { MachineStatusOption, Technician } from '../types/types'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  form: ReturnType<typeof useForm<any>>;
  machineFieldsEnabled: boolean;
  technicians: Technician[];
  machineTypes: string[];
  machineStatuses: MachineStatusOption[];
}>()

const statusOptions = [
  { value: 'for check up', label: 'For Check Up' },
  { value: 'for approval', label: 'For Approval' },
  { value: 'successful', label: 'Successful' },
  { value: 'failed', label: 'Failed' },
  { value: 'dropped', label: 'Dropped' },
  { value: 'inprogress', label: 'In Progress' },
  { value: 'on-hold', label: 'On-hold' },
  { value: 'closed', label: 'Closed' },
  { value: 'completed', label: 'Completed' },
]
</script>

<template>
  <div class="space-y-6">
    <!-- Client Information -->
    <div class="space-y-4 bg-white p-6 rounded-2xl shadow-sm">
      <h2 class="text-base font-semibold text-gray-700">Client Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-600">Name / Company</label>
          <input v-model="form.client" class="input-field w-full" placeholder="Enter client or company name" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Contact Person</label>
          <input v-model="form.contact_person" class="input-field w-full" placeholder="Person in charge" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Address</label>
          <input v-model="form.address" class="input-field w-full" placeholder="Complete address" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Contact No.</label>
          <input v-model="form.contact_no" class="input-field w-full" placeholder="09XXXXXXXXX" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Branch / Department</label>
          <input v-model="form.department" class="input-field w-full" placeholder="Department or location" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Technicians</label>
          <Multiselect v-model="form.technicians" :options="technicians" :multiple="true" :close-on-select="false"
            :clear-on-select="false" :preserve-search="true" label="first_name" track-by="id"
            :custom-label="tech => `${tech.first_name} ${tech.middle_name} ${tech.last_name} ${tech.suffix}`"
            placeholder="Select technicians" class="w-full" />
        </div>
      </div>
    </div>

    <!-- Schedule & Status -->
    <div class="space-y-4 bg-white p-6 rounded-2xl shadow-sm">
      <h2 class="text-base font-semibold text-gray-700">Schedule & Status</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs text-gray-600">Date</label>
          <input type="date" v-model="form.date" class="input-field w-full" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Time</label>
          <input type="time" v-model="form.time" class="input-field w-full" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Status</label>
          <select v-model="form.status" class="input-field w-full">
            <option disabled value="">Select status</option>
            <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Machines -->
    <div v-for="(machine, index) in form.machine_infos" :key="index"
      class="bg-gray-50 p-6 rounded-2xl shadow-inner border space-y-4 w-full">
      <h3 class="text-lg font-medium text-gray-700">
        Machine #{{ index + 1 }}
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-600">Machine Type</label>
          <select v-model="machine.machine_type" :disabled="!machineFieldsEnabled" class="w-full input-field">
            <option disabled value="">Select machine type</option>
            <option v-for="type in machineTypes" :key="type" :value="type">{{ type }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600">Model</label>
          <input v-model="machine.model" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div>
          <label class="block text-sm text-gray-600">Serial No</label>
          <input v-model="machine.serial_no" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div>
          <label class="block text-sm text-gray-600">Tag No</label>
          <input v-model="machine.tag_no" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600">Machine Problem</label>
          <textarea v-model="machine.machine_problem" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600">Service Performed</label>
          <textarea v-model="machine.service_performed" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600">Recommendation</label>
          <textarea v-model="machine.recommendation" :disabled="!machineFieldsEnabled" class="w-full input-field" />
        </div>
        <div>
          <label class="block text-sm text-gray-600">Machine Status</label>
          <select v-model="machine.machine_status" :disabled="!machineFieldsEnabled" class="w-full input-field">
            <option disabled value="">Select status</option>
            <option v-for="status in machineStatuses" :key="status.value" :value="status.value">
              {{ status.label }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="form.machine_infos.length > 1" class="text-right mt-2">
        <button type="button" @click="form.machine_infos.splice(index, 1)" class="text-red-600 hover:underline text-sm">
          ✖ Remove Machine
        </button>
      </div>
    </div>

    <div class="mt-4">
      <button type="button" @click="form.machine_infos.push({
        machine_type: '',
        model: '',
        serial_no: '',
        tag_no: '',
        machine_problem: '',
        service_performed: '',
        recommendation: '',
        machine_status: ''
      })" class="text-blue-600 hover:underline text-sm">
        + Add another machine
      </button>
    </div>
  </div>
</template>

<style scoped>
.input-field {
  @apply border rounded-xl px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition;
}
</style>
