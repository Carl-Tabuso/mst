<template>
  <AppLayout>
    <div class="w-full px-4 py-8 md:px-12 xl:px-20">
      <h3 class="mb-8 text-4xl font-bold text-gray-700">Add IT Service</h3>
      <form
        @submit.prevent="submitForm"
        class="w-full space-y-8 text-sm"
      >
        <ITServiceFields
          ref="formComponent"
          :form="form"
          :technicians="props.technicians"
          :machineTypes="props.machineTypes"
          :machineStatuses="props.machineStatuses"
        />

        <div class="mt-6 flex justify-end gap-4 px-6 md:col-span-2">
          <button
            type="button"
            @click="goBack"
            class="rounded-xl bg-gray-200 px-6 py-2 text-gray-700 shadow-md transition hover:bg-gray-300"
          >
            Cancel
          </button>

          <button
            type="submit"
            class="rounded-xl bg-blue-900 px-6 py-2 text-white shadow-md transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Add
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

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ITServiceFields from '../components/ITServiceFields.vue'
import { useITServiceForm } from '../helpers/useITServiceForm'
import { ITServiceFormProps } from '../types/types'

const props = defineProps<ITServiceFormProps>()

const { form, formComponent, submitForm } = useITServiceForm(props)

function goBack() {
  window.history.back()
}
</script>
