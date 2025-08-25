<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ITServiceFields from '../components/ITServiceFields.vue';
import LastOnsiteFields from '../components/LastOnsiteFields.vue';
import ITServiceFirstOnsiteFieldsForEdit from '../components/ITServiceFirstOnsiteFieldsForEdit.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Technician, MachineStatusOption } from '../types/types';

interface FormComponentInstance {
    validateForm(): boolean;
    isValidForm: boolean;
    errors: { [key: string]: string };
    showValidation: any;
}

interface Props {
    jobOrderId: number;
    jobOrderNumber: string;
    serviceId: number;
    reportId: number;
    technicians: Technician[];
    machineTypes: string[];
    firstOnsiteStatuses: MachineStatusOption[];
    finalOnsiteStatuses: MachineStatusOption[];
    jobOrder: {
        id: number;
        client: string;
        address: string;
        department: string;
        contact_no: string;
        contact_person: string;
        date_time?: string;
        job_order_no: string;
    };
    itService: {
        id: number;
        technician_id: string;
        machine_type: string;
        model: string;
        serial_no: string;
        tag_no: string;
        machine_problem: string;
        status: string;
    };
    firstOnsiteReport: {
        id: number;
        service_performed: string;
        recommendation: string;
        machine_status: string;
        attached_file?: string;
    };
    existingFinalReport: {
        id: number;
        service_performed: string;
        parts_replaced: string;
        final_remark: string;
        final_machine_status: string;
        attached_file?: string;
    };
    isEdit: boolean;
}

const props = defineProps<Props>();

const initialFormComponent = ref<FormComponentInstance | null>(null);
const firstOnsiteFormComponent = ref<FormComponentInstance | null>(null);
const finalOnsiteFormComponent = ref<FormComponentInstance | null>(null);

const dateTime = props.jobOrder.date_time ? new Date(props.jobOrder.date_time) : null;

const form = useForm({
    client: props.jobOrder?.client || '',
    address: props.jobOrder?.address || '',
    department: props.jobOrder?.department || '',
    contact_no: props.jobOrder?.contact_no || '',
    contact_person: props.jobOrder?.contact_person || '',
    date: dateTime ? dateTime.toISOString().split('T')[0] : '',
    time: dateTime ? dateTime.toTimeString().slice(0, 5) : '',
    technician_id: props.itService?.technician_id || '',
    machine_type: props.itService?.machine_type || '',
    model: props.itService?.model || '',
    serial_no: props.itService?.serial_no || '',
    tag_no: props.itService?.tag_no || '',
    machine_problem: props.itService?.machine_problem || '',

    first_service_performed: props.firstOnsiteReport?.service_performed || '',
    recommendation: props.firstOnsiteReport?.recommendation || '',
    machine_status: props.firstOnsiteReport?.machine_status || '',
    attached_file: null as File | null,

    service_performed: props.existingFinalReport?.service_performed || '',
    parts_replaced: props.existingFinalReport?.parts_replaced || '',
    final_remark: props.existingFinalReport?.final_remark || '',
    final_machine_status: props.existingFinalReport?.final_machine_status || '',
});

const submitForm = () => {
    let isValidInitial = initialFormComponent.value?.validateForm() ?? true;
    let isValidFirstOnsite = firstOnsiteFormComponent.value?.validateForm() ?? true;
    let isValidFinalOnsite = finalOnsiteFormComponent.value?.validateForm() ?? true;

    const isValid = isValidInitial && isValidFirstOnsite && isValidFinalOnsite;

    if (!isValid) {
        setTimeout(() => {
            const firstError = document.querySelector('.border-red-500') as HTMLElement
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                })
                if (['INPUT', 'SELECT', 'TEXTAREA'].includes(firstError.tagName)) {
                    firstError.focus()
                }
            }
        }, 100)
        return
    }

    const jobOrderId = String(props.jobOrderId);
    const formData = form.data();

    if (!form.attached_file) {
        delete formData.attached_file;
    }
    if (!form.first_attached_file) {
        delete formData.first_attached_file;
    }

    router.post(
        route('job_order.it_service.corrections.store', {
            jobOrder: jobOrderId,
        }),
        {
            fields: formData,
        },
        {
            forceFormData: true,
            onSuccess: () => {
                if (initialFormComponent.value?.showValidation) {
                    initialFormComponent.value.showValidation.value = false
                }
                if (firstOnsiteFormComponent.value?.showValidation) {
                    firstOnsiteFormComponent.value.showValidation.value = false
                }
                if (finalOnsiteFormComponent.value?.showValidation) {
                    finalOnsiteFormComponent.value.showValidation.value = false
                }
                
            },
        }
    );
};

function goBack() {
    window.history.back();
}

</script>

<template>
    <AppLayout title="Edit Final Onsite IT Service">
        <div class="px-4 md:px-12 xl:px-20 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">Edit Final Onsite Visit</h1>
                    <p class="text-sm text-gray-500 mt-1">Update the complete service report</p>
                </div>
                <div class="text-lg font-semibold text-blue-900">
                    Job Order: {{ props.jobOrderId }}
                </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Initial Stage Fields -->
                <div class="px-6">
                    <h2 class="text-lg font-medium text-gray-700 mb-4">Initial Information</h2>
                    <ITServiceFields ref="initialFormComponent" :form="form" :technicians="props.technicians"
                        :machineTypes="props.machineTypes" />
                </div>

                <!-- First Onsite Fields -->
                <div class="px-6">
                    <ITServiceFirstOnsiteFieldsForEdit ref="firstOnsiteFormComponent" :form="form"
                        :machineStatuses="props.firstOnsiteStatuses" />

                    <div v-if="props.firstOnsiteReport?.attached_file" class="mt-4 bg-white p-4 rounded-xl">
                        <h3 class="text-sm font-medium text-blue-800 mb-2">Current First Onsite Report File</h3>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <a :href="route('job_order.it_service.report.download', {
                                jobOrder: jobOrder.id,
                                reportId: firstOnsiteReport.id,
                            })" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm underline">
                                View Current First Onsite Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Final Onsite Fields -->
                <div class="px-6">
                    <LastOnsiteFields ref="finalOnsiteFormComponent" :form="form"
                        :machineStatuses="props.finalOnsiteStatuses" />
                </div>

                <!-- Submit Buttons -->
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
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Updating...
                        </span>
                        <span v-else>Request Update </span>
                    </button>
                </div>

                <!-- Success Message -->
                <div v-if="form.recentlySuccessful" class="text-center">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Final onsite report updated successfully!
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.input-field {
    @apply border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-blue-200 focus:border-blue-200 transition-colors;
}
</style>