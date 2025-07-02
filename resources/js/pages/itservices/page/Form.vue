<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useITServiceForm } from '../helpers/useITServiceForm';
import ITServiceFields from '../components/ITServiceFields.vue';
import { ITServiceFormProps } from '../types/types';

const props = defineProps<ITServiceFormProps>();
const { form, machineFieldsEnabled, hasDuplicate, isValidMachine, submitForm } = useITServiceForm(props);
</script>

<template>
  <AppLayout>
    <div class="w-full px-4 md:px-12 xl:px-20 py-8">
      <h1 class="text-2xl font-semibold mb-8 text-gray-700">IT Service Form</h1>
      <form @submit.prevent="submitForm" class="space-y-8 text-sm w-full">
        <ITServiceFields :form="form" :machineFieldsEnabled="machineFieldsEnabled" :technicians="props.technicians"
          :machineTypes="props.machineTypes" :machineStatuses="props.machineStatuses" />

        <div v-if="hasDuplicate" class="md:col-span-2 text-yellow-600 text-sm mt-1">
          A service with this machine type, model, serial number, and tag number already exists.
        </div>

        <div class="md:col-span-2 flex justify-end mt-6">
          <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow-md transition disabled:opacity-60 disabled:cursor-not-allowed"
            :disabled="form.processing || (!isValidMachine && machineFieldsEnabled)">
            Submit
          </button>
        </div>

        <div v-if="form.recentlySuccessful" class="md:col-span-2 text-green-600 mt-2">Saved!</div>
      </form>
    </div>
  </AppLayout>
</template>
