<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ITServiceFields from '../components/ITServiceFields.vue'
import { useITServiceForm } from '../helpers/useITServiceForm'
import { ITServiceFormProps } from '../types/types'

const props = defineProps<ITServiceFormProps>()
const { form, machineFieldsEnabled, hasDuplicate, isValidMachine, submitForm } =
  useITServiceForm(props)
</script>

<template>
  <AppLayout>
    <div class="w-full px-4 py-8 md:px-12 xl:px-20">
      <h1 class="mb-8 text-2xl font-semibold text-gray-700">IT Service Form</h1>
      <form
        @submit.prevent="submitForm"
        class="w-full space-y-8 text-sm"
      >
        <ITServiceFields
          :form="form"
          :machineFieldsEnabled="machineFieldsEnabled"
          :technicians="props.technicians"
          :machineTypes="props.machineTypes"
          :machineStatuses="props.machineStatuses"
        />

        <div
          v-if="hasDuplicate"
          class="mt-1 text-sm text-yellow-600 md:col-span-2"
        >
          A service with this machine type, model, serial number, and tag number
          already exists.
        </div>

        <div class="mt-6 flex justify-end md:col-span-2">
          <button
            type="submit"
            class="rounded-xl bg-blue-600 px-6 py-2 text-white shadow-md transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="
              form.processing || (!isValidMachine && machineFieldsEnabled)
            "
          >
            Submit
          </button>
        </div>

        <div
          v-if="form.recentlySuccessful"
          class="mt-2 text-green-600 md:col-span-2"
        >
          Saved!
        </div>
      </form>
    </div>
  </AppLayout>
</template>
