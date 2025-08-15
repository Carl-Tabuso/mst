<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ITServiceReport, ITService, JobOrder } from '../types/types';
import { ArrowLeft } from 'lucide-vue-next'

interface Props {
    report: ITServiceReport;
    jobOrder: JobOrder;
    itService: ITService;
}

defineProps<Props>();

const full_name = (technician: ITService['technician']) => {
    if (!technician) return 'N/A';
    const { first_name, middle_name, last_name, suffix } = technician;
    return `${first_name} ${middle_name ? middle_name + ' ' : ''}${last_name}${suffix ? ', ' + suffix : ''}`;
};

function goBack() {
    window.history.back();
}
</script>

<template>
    <AppLayout title="First Onsite Service Report">
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="goBack" class="p-2 rounded-lg hover:bg-gray-200 transition"
                                title="Go Back">
                                <ArrowLeft class="w-5 h-5 text-gray-600" />
                            </button>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">First Onsite Visit Report</h1>
                                <p class="text-gray-600 mt-1">Initial service assessment and recommendations</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-semibold text-sm">
                                {{ report.onsite_type?.toUpperCase() || 'INITIAL' }}
                            </div>
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold text-sm">
                                {{ report.machine_status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-8">
                    <!-- Job Order Summary -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-white">Job Order Summary</h2>
                                    <p class="text-slate-100 text-sm">Client Information & Service Details</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Client</label>
                                    <p class="text-lg font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg">{{
                                        jobOrder.client }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Contact
                                        Person</label>
                                    <p class="text-lg font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg">{{
                                        jobOrder.contact_person }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Department</label>
                                    <p class="text-lg font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg">{{
                                        jobOrder.department }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Technician</label>
                                    <div
                                        class="flex items-center space-x-2 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-900">{{
                                            full_name(itService.technician) }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Date &
                                        Time</label>
                                    <div class="flex items-center space-x-2 bg-gray-50 p-3 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-900">{{ jobOrder.date_time ??
                                            'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-2 sm:col-span-2 lg:col-span-3">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Address</label>
                                    <div class="flex items-start space-x-2 bg-gray-50 p-3 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-900">{{ jobOrder.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Machine Details -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-white">Machine Information</h2>
                                    <p class="text-purple-100 text-sm">Equipment specifications and reported issues
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Type</label>
                                    <div class="bg-purple-50 border border-purple-100 p-3 rounded-lg">
                                        <p class="text-lg font-semibold text-gray-900">{{ itService.machine_type }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide">Model</label>
                                    <p class="text-lg font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg">{{
                                        itService.model }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Serial
                                        Number</label>
                                    <div class="bg-gray-50 p-3 rounded-lg font-mono">
                                        <p class="text-lg font-semibold text-gray-900">{{ itService.serial_no }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tag
                                        Number</label>
                                    <div class="bg-gray-50 p-3 rounded-lg font-mono">
                                        <p class="text-lg font-semibold text-gray-900">{{ itService.tag_no }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Reported
                                        Problem</label>
                                    <div class="bg-red-50 border border-red-100 p-4 rounded-lg">
                                        <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{
                                            itService.machine_problem }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Report -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-white">First Onsite Service Report</h2>
                                    <p class="text-green-100 text-sm">Technician findings and recommendations</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-3">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Service
                                    Performed</label>
                                <div class="bg-green-50 border border-green-100 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{
                                            report.service_performed }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Final
                                    Machine Status</label>
                                <div class="bg-green-50 border border-green-100 p-3 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-900">{{
                                            report.machine_status ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-sm font-medium text-gray-500 uppercase tracking-wide">Recommendation</label>
                                <div class="bg-amber-50 border border-amber-100 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                            </path>
                                        </svg>
                                        <p class="text-gray-900 whitespace-pre-line leading-relaxed">{{
                                            report.recommendation }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="report.attached_file" class="space-y-3">
                                <label class="text-sm font-medium text-gray-500 uppercase tracking-wide">Technician's
                                    Report</label>
                                <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg">
                                    <a :href="`/storage/${report.attached_file}`" target="_blank"
                                        class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                            </path>
                                        </svg>
                                        <span>View Attached Report</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>