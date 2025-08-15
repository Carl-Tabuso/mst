<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { useLastOnsiteForm } from '../helpers/useLastOnsiteForm'
import LastOnsiteFields from '../components/LastOnsiteFields.vue'
import { MachineStatusOption } from '../types/types';

const props = defineProps<{
    jobOrderId: number,
    serviceId: number,
    jobOrderNumber: string
    machineStatuses: MachineStatusOption[],
}>()

const { form, formComponent, submitForm } = useLastOnsiteForm(props.jobOrderId, props.serviceId)

function goBack() {
    window.history.back();
}
</script>

<template>
    <AppLayout>
        <div class="px-4 md:px-12 xl:px-20 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">Final Onsite Visit</h1>
                    <p class="text-sm text-gray-500 mt-1">Complete the final service report</p>
                </div>
                <div class="text-lg font-semibold text-blue-900">
                    Job Order: {{ jobOrderNumber }}
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Use the formComponent ref from the composable -->
                <LastOnsiteFields ref="formComponent" :form="form" :machineStatuses="machineStatuses" />

                <div class="flex justify-end gap-4 mt-8 px-6">
                    <button type="button" @click="goBack"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-xl shadow-md transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-xl shadow-md transition disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="form.processing">
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Submitting...
                        </span>
                        <span v-else">Submit Final Report</span>
                    </button>
                </div>

                <div v-if="form.recentlySuccessful" class="text-center">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Final report submitted successfully!
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>