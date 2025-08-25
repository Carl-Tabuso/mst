<template>
  <AppLayout>
    <div class="w-full px-4 md:px-12 xl:px-20 py-8">
      <h3 class="text-4xl font-bold mb-8 text-gray-700">Add IT Service</h3>
      <form @submit.prevent="submitForm" class="space-y-8 text-sm w-full">
        <ITServiceFields 
          ref="formComponent" 
          :form="form" 
          :technicians="props.technicians"
          :machineTypes="props.machineTypes" 
          :machineStatuses="props.machineStatuses" 
        />

        <div class="md:col-span-2 flex justify-end gap-4 mt-6 px-6">
          <button type="button" @click="goBack"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-xl shadow-md transition">
            Cancel
          </button>

          <button type="submit"
            class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-xl shadow-md transition disabled:opacity-60 disabled:cursor-not-allowed"
            :disabled="form.processing">
            Add
          </button>
        </div>

        <div v-if="form.recentlySuccessful" class="mt-2 text-green-600 md:col-span-2">
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

const props = defineProps<ITServiceFormProps>();

const { form, formComponent, submitForm } = useITServiceForm(props);

function goBack() {
  window.history.back();
}

</script>