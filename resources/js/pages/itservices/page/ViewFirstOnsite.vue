<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ArrowLeft } from 'lucide-vue-next'
import { ITService, ITServiceReport, JobOrder } from '../types/types'

interface Props {
  report: ITServiceReport
  jobOrder: JobOrder
  itService: ITService
}

defineProps<Props>()

const full_name = (technician: ITService['technician']) => {
  if (!technician) return 'N/A'
  const { first_name, middle_name, last_name, suffix } = technician
  return `${first_name} ${middle_name ? middle_name + ' ' : ''}${last_name}${suffix ? ', ' + suffix : ''}`
}

function goBack() {
  window.history.back()
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
  if (!filePath) return 'Unknown file'
  const parts = filePath.split(/[/\\]/)
  return parts[parts.length - 1] || 'Unknown file'
}

const getFileExtension = (fileName: string) => {
  if (!fileName) return ''
  const parts = fileName.split('.')
  return parts.length > 1 ? parts.pop()?.toLowerCase() || '' : ''
}

const getFileIcon = (fileName: string) => {
  const ext = getFileExtension(fileName)

  if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(ext)) {
    return 'image-icon'
  } else if (ext === 'pdf') {
    return 'pdf-icon'
  } else if (['doc', 'docx'].includes(ext)) {
    return 'document-icon'
  }
  return 'file-icon'
}
</script>

<template>
  <AppLayout title="First Onsite Service Report">
    <div
      class="min-h-screen from-blue-50 to-indigo-100 transition-colors duration-200 dark:from-gray-900 dark:to-gray-800"
    >
      <div
        class="container mx-auto px-3 py-4 sm:px-4 sm:py-6 md:px-6 lg:px-8 lg:py-8"
      >
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <div
            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
          >
            <div class="flex w-full items-center gap-3 sm:w-auto">
              <button
                type="button"
                @click="goBack"
                class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-700"
                title="Go Back"
              >
                <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
              </button>
              <div class="flex-1 sm:flex-none">
                <h1
                  class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl"
                >
                  First Onsite Visit Report
                </h1>
                <p
                  class="mt-1 text-sm text-gray-600 dark:text-gray-300 sm:text-base"
                >
                  Initial service assessment and recommendations
                </p>
              </div>
            </div>
            <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:gap-3">
              <div
                class="rounded-full border bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-800 dark:border-blue-800 dark:bg-blue-900/50 dark:text-blue-200 sm:px-4 sm:text-sm"
              >
                {{ report.onsite_type?.toUpperCase() || 'INITIAL' }}
              </div>
              <div
                class="rounded-full border bg-green-100 px-3 py-2 text-xs font-semibold text-green-800 dark:border-green-800 dark:bg-green-900/50 dark:text-green-200 sm:px-4 sm:text-sm"
              >
                {{ report.machine_status }}
              </div>
            </div>
          </div>
        </div>

        <div class="grid gap-6 sm:gap-8">
          <!-- Job Order Summary -->
          <div
            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl transition-colors duration-200 dark:border-gray-700 dark:bg-gray-800"
          >
            <div
              class="bg-gradient-to-r from-slate-600 to-slate-700 px-4 py-4 dark:from-slate-700 dark:to-slate-800 sm:px-6"
            >
              <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-white/20 p-2 dark:bg-white/30">
                  <svg
                    class="h-5 w-5 text-white sm:h-6 sm:w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    ></path>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <h2
                    class="truncate text-lg font-semibold text-white sm:text-xl"
                  >
                    Job Order Summary
                  </h2>
                  <p class="text-xs text-slate-100 sm:text-sm">
                    Client Information & Service Details
                  </p>
                </div>
              </div>
            </div>
            <div class="p-4 sm:p-6">
              <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3"
              >
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Client</label
                  >
                  <p
                    class="break-words rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
                  >
                    {{ jobOrder.client }}
                  </p>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Contact Person</label
                  >
                  <p
                    class="break-words rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
                  >
                    {{ jobOrder.contact_person }}
                  </p>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Department</label
                  >
                  <p
                    class="break-words rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
                  >
                    {{ jobOrder.department }}
                  </p>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Technician</label
                  >
                  <div
                    class="flex items-center space-x-2 rounded-lg border border-blue-100 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-900/30"
                  >
                    <svg
                      class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 break-words text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ full_name(itService.technician) }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Date & Time</label
                  >
                  <div
                    class="flex items-center space-x-2 rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                  >
                    <svg
                      class="h-5 w-5 flex-shrink-0 text-gray-600 dark:text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 break-words text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ formatDate(jobOrder.date_time) ?? 'N/A' }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2 sm:col-span-2 lg:col-span-3">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Address</label
                  >
                  <div
                    class="flex items-start space-x-2 rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                  >
                    <svg
                      class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 break-words text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ jobOrder.address }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Machine Details -->
          <div
            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl transition-colors duration-200 dark:border-gray-700 dark:bg-gray-800"
          >
            <div
              class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-4 dark:from-purple-700 dark:to-purple-800 sm:px-6"
            >
              <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-white/20 p-2 dark:bg-white/30">
                  <svg
                    class="h-5 w-5 text-white sm:h-6 sm:w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                    ></path>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <h2
                    class="truncate text-lg font-semibold text-white sm:text-xl"
                  >
                    Machine Information
                  </h2>
                  <p class="text-xs text-purple-100 sm:text-sm">
                    Equipment specifications and reported issues
                  </p>
                </div>
              </div>
            </div>
            <div class="p-4 sm:p-6">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Type</label
                  >
                  <div
                    class="rounded-lg border border-purple-100 bg-purple-50 p-3 dark:border-purple-800 dark:bg-purple-900/30"
                  >
                    <p
                      class="break-words text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ itService.machine_type }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Model</label
                  >
                  <p
                    class="break-words rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
                  >
                    {{ itService.model }}
                  </p>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Serial Number</label
                  >
                  <div
                    class="rounded-lg bg-gray-50 p-3 font-mono dark:bg-gray-700"
                  >
                    <p
                      class="break-all text-sm font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ itService.serial_no }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Tag Number</label
                  >
                  <div
                    class="rounded-lg bg-gray-50 p-3 font-mono dark:bg-gray-700"
                  >
                    <p
                      class="break-all text-sm font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ itService.tag_no }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2 sm:col-span-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Reported Problem</label
                  >
                  <div
                    class="rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/30"
                  >
                    <p
                      class="whitespace-pre-line text-sm leading-relaxed text-gray-900 dark:text-white sm:text-base"
                    >
                      {{ itService.machine_problem }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Service Report -->
          <div
            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl transition-colors duration-200 dark:border-gray-700 dark:bg-gray-800"
          >
            <div
              class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-4 dark:from-green-700 dark:to-green-800 sm:px-6"
            >
              <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-white/20 p-2 dark:bg-white/30">
                  <svg
                    class="h-5 w-5 text-white sm:h-6 sm:w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                    ></path>
                  </svg>
                </div>
                <div class="min-w-0 flex-1">
                  <h2
                    class="truncate text-lg font-semibold text-white sm:text-xl"
                  >
                    First Onsite Service Report
                  </h2>
                  <p class="text-xs text-green-100 sm:text-sm">
                    Technician findings and recommendations
                  </p>
                </div>
              </div>
            </div>
            <div class="space-y-4 p-4 sm:space-y-6 sm:p-6">
              <div class="space-y-3">
                <label
                  class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                  >Service Performed</label
                >
                <div
                  class="rounded-lg border border-green-100 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/30"
                >
                  <div class="flex items-start space-x-2">
                    <svg
                      class="mt-0.5 h-5 w-5 flex-shrink-0 text-green-600 dark:text-green-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 whitespace-pre-line text-sm leading-relaxed text-gray-900 dark:text-white sm:text-base"
                    >
                      {{ report.service_performed }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="space-y-2">
                <label
                  class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                  >Final Machine Status</label
                >
                <div
                  class="rounded-lg border border-green-100 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/30"
                >
                  <div class="flex items-center space-x-2">
                    <svg
                      class="h-5 w-5 flex-shrink-0 text-green-600 dark:text-green-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 break-words text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ report.machine_status ?? 'N/A' }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="space-y-3">
                <label
                  class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                  >Recommendation</label
                >
                <div
                  class="rounded-lg border border-amber-100 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/30"
                >
                  <div class="flex items-start space-x-2">
                    <svg
                      class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600 dark:text-amber-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                      ></path>
                    </svg>
                    <p
                      class="min-w-0 flex-1 whitespace-pre-line text-sm leading-relaxed text-gray-900 dark:text-white sm:text-base"
                    >
                      {{ report.recommendation }}
                    </p>
                  </div>
                </div>
              </div>

              <div
                v-if="report.attached_file"
                class="space-y-3"
              >
                <label
                  class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                  >Technician's Report</label
                >
                <div
                  class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/30"
                >
                  <a
                    :href="
                      route('job_order.it_service.report.download', {
                        jobOrder: jobOrder.id,
                        reportId: report.id,
                      })
                    "
                    target=" _blank"
                    class="inline-flex items-center space-x-2 break-words text-sm font-medium text-blue-600 transition-colors duration-200 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 sm:text-base"
                  >
                    <svg
                      class="h-5 w-5 flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                      ></path>
                    </svg>
                    <span class="min-w-0 flex-1">View Attached Report</span>
                    <svg
                      class="h-4 w-4 flex-shrink-0"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                      ></path>
                    </svg>
                  </a>
                  <div
                    class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                    v-if="report.attached_file"
                  >
                    <span>{{ getFileName(report.attached_file) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
