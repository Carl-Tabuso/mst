<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { JobOrder, ITService, ITServiceReport } from '../types/types';
import { ArrowLeft } from 'lucide-vue-next'

interface Props {
    jobOrder: JobOrder;
    itService: ITService;
    firstOnsiteReport: ITServiceReport | null;
    finalOnsiteReport: ITServiceReport | null;
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

const formatDate = (date: string | null) => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    }).format(d)
}

const getFileName = (filePath: string) => {
    if (!filePath) return 'Unknown file';
    const parts = filePath.split(/[/\\]/);
    return parts[parts.length - 1] || 'Unknown file';
};

const getFileExtension = (fileName: string) => {
    if (!fileName) return '';
    const parts = fileName.split('.');
    return parts.length > 1 ? parts.pop()?.toLowerCase() || '' : '';
};

const getFileIcon = (fileName: string) => {
    const ext = getFileExtension(fileName);

    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(ext)) {
        return 'image-icon';
    } else if (ext === 'pdf') {
        return 'pdf-icon';
    } else if (['doc', 'docx'].includes(ext)) {
        return 'document-icon';
    }
    return 'file-icon';
};
</script>

<template>
    <AppLayout title="Final Onsite Service Report">
        <div class="min-h-screen from-green-50 to-emerald-100 dark:from-gray-900 dark:to-gray-800">
            <div class="container mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="goBack"
                                class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                title="Go Back">
                                <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                            </button>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Complete
                                    Service Report</h1>
                                <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm sm:text-base">Comprehensive
                                    service history and final outcomes</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <div
                                class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 px-3 sm:px-4 py-2 rounded-full font-semibold text-xs sm:text-sm">
                                SERVICE COMPLETE
                            </div>
                            <div v-if="finalOnsiteReport"
                                class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 px-3 sm:px-4 py-2 rounded-full font-semibold text-xs sm:text-sm">
                                {{ finalOnsiteReport.final_machine_status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 sm:gap-8">
                    <!-- Job Order Summary -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-slate-600 to-slate-700 dark:from-slate-700 dark:to-slate-800 px-4 sm:px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 dark:bg-white/10 p-2 rounded-lg">
                                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-semibold text-white">Job Order Information</h2>
                                    <p class="text-slate-100 dark:text-slate-200 text-xs sm:text-sm">Client details and
                                        service assignment</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Client</label>
                                    <p
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        {{
                                        jobOrder.client }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Contact
                                        Person</label>
                                    <p
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        {{
                                        jobOrder.contact_person }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Department</label>
                                    <p
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        {{
                                        jobOrder.department }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Technician</label>
                                    <div
                                        class="flex items-center space-x-2 bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg border border-blue-100 dark:border-blue-800">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            full_name(itService.technician) }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Service
                                        Date</label>
                                    <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            formatDate(jobOrder.date_time) ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2 sm:col-span-2 xl:col-span-3">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Service
                                        Address</label>
                                    <div class="flex items-start space-x-2 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                        </svg>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            jobOrder.address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Machine Information -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-purple-600 to-purple-700 dark:from-purple-700 dark:to-purple-800 px-4 sm:px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 dark:bg-white/10 p-2 rounded-lg">
                                    <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-semibold text-white">Equipment Details</h2>
                                    <p class="text-purple-100 dark:text-purple-200 text-xs sm:text-sm">Machine
                                        specifications and initial problem</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Machine
                                        Type</label>
                                    <div
                                        class="bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800 p-3 rounded-lg">
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            itService.machine_type }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Model</label>
                                    <p
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        {{
                                        itService.model }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Serial
                                        Number</label>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg font-mono">
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            itService.serial_no }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tag
                                        Number</label>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg font-mono">
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            itService.tag_no }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2 sm:col-span-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Initial
                                        Problem Report</label>
                                    <div
                                        class="bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800 p-4 rounded-lg">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                            <p
                                                class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                                {{
                                                itService.machine_problem }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- First Onsite Visit -->
                    <div v-if="firstOnsiteReport"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-4 sm:px-6 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-white/20 dark:bg-white/10 p-2 rounded-lg">
                                        <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-semibold text-white">First Onsite Assessment
                                        </h2>
                                        <p class="text-blue-100 dark:text-blue-200 text-xs sm:text-sm">Initial diagnosis
                                            and preliminary work</p>
                                    </div>
                                </div>
                                <div
                                    class="bg-white/20 dark:bg-white/10 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-medium self-start sm:self-auto">
                                    STEP 1
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Visit
                                        Type</label>
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 p-3 rounded-lg">
                                        <p
                                            class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white capitalize">
                                            {{
                                            firstOnsiteReport.onsite_type ?? 'Initial' }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Machine
                                        Status</label>
                                    <div
                                        class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-100 dark:border-yellow-800 p-3 rounded-lg">
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">{{
                                            firstOnsiteReport.machine_status ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Service
                                    Performed</label>
                                <div
                                    class="bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p
                                            class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                            {{
                                            firstOnsiteReport.service_performed ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Technician
                                    Recommendation</label>
                                <div
                                    class="bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                            </path>
                                        </svg>
                                        <p
                                            class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                            {{
                                            firstOnsiteReport.recommendation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="firstOnsiteReport.attached_file" class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                    First Visit Report
                                </label>
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 p-4 rounded-lg">
                                    <a :href="route('job_order.it_service.report.download', {
                                        jobOrder: jobOrder.id,
                                        reportId: firstOnsiteReport.id
                                    })" target="_blank" class="inline-flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors text-sm sm:text-base">

                                        <!-- File icon -->
                                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                            </path>
                                        </svg>

                                        <span>View First Visit Report</span>

                                        <svg class="w-3 sm:w-4 h-3 sm:h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    </a>

                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                        v-if="firstOnsiteReport.attached_file">
                                        <span>{{ getFileName(firstOnsiteReport.attached_file) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Onsite Visit -->
                    <div v-if="finalOnsiteReport"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-700 dark:to-emerald-800 px-4 sm:px-6 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-white/20 dark:bg-white/10 p-2 rounded-lg">
                                        <svg class="w-5 sm:w-6 h-5 sm:h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-semibold text-white">Final Service Completion
                                        </h2>
                                        <p class="text-emerald-100 dark:text-emerald-200 text-xs sm:text-sm">Resolution
                                            and final machine status</p>
                                    </div>
                                </div>
                                <div
                                    class="bg-white/20 dark:bg-white/10 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-medium self-start sm:self-auto">
                                    COMPLETED
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Visit
                                        Type</label>
                                    <div
                                        class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 p-3 rounded-lg">
                                        <p
                                            class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white capitalize">
                                            {{
                                            finalOnsiteReport.onsite_type ?? 'Final' }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Final
                                        Machine Status</label>
                                    <div
                                        class="bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800 p-3 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                                {{
                                                finalOnsiteReport.final_machine_status ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Final
                                    Service Performed</label>
                                <div
                                    class="bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p
                                            class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                            {{
                                            finalOnsiteReport.service_performed ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Parts
                                    Replaced</label>
                                <div
                                    class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                        <p
                                            class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                            {{
                                            finalOnsiteReport.parts_replaced ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label
                                    class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Final
                                    Remarks</label>
                                <div
                                    class="bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 p-4 rounded-lg">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mt-0.5 flex-shrink-0"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                            </path>
                                        </svg>
                                        <p
                                            class="text-gray-900 dark:text-gray-100 whitespace-pre-line leading-relaxed text-sm sm:text-base">
                                            {{
                                            finalOnsiteReport.final_remark ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            
                        </div>
                    </div>

                    <!-- Service Summary -->
                    <div
                        class="bg-gradient-to-r from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-2xl text-white p-6 sm:p-8">
                        <div class="text-center">
                            <div
                                class="bg-green-500 dark:bg-green-600 w-12 sm:w-16 h-12 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 sm:w-8 h-6 sm:h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold mb-2">Service Completed Successfully</h3>
                            <p class="text-gray-300 dark:text-gray-400 text-base sm:text-lg">All scheduled maintenance
                                and repairs have been completed.</p>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                                <div class="bg-white/10 dark:bg-white/5 backdrop-blur rounded-lg p-3 sm:p-4">
                                    <div class="text-xl sm:text-2xl font-bold text-green-400 dark:text-green-300">{{
                                        firstOnsiteReport ? '2' : '1' }}</div>
                                    <div class="text-xs sm:text-sm text-gray-300 dark:text-gray-400">Service Visits
                                    </div>
                                </div>
                                <div class="bg-white/10 dark:bg-white/5 backdrop-blur rounded-lg p-3 sm:p-4">
                                    <div
                                        class="text-lg sm:text-2xl font-bold text-blue-400 dark:text-blue-300 break-words">
                                        {{ itService.machine_type }}</div>
                                    <div class="text-xs sm:text-sm text-gray-300 dark:text-gray-400">Equipment Type
                                    </div>
                                </div>
                                <div class="bg-white/10 dark:bg-white/5 backdrop-blur rounded-lg p-3 sm:p-4">
                                    <div
                                        class="text-base sm:text-2xl font-bold text-purple-400 dark:text-purple-300 break-words">
                                        {{ full_name(itService.technician) }}</div>
                                    <div class="text-xs sm:text-sm text-gray-300 dark:text-gray-400">Technician</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>