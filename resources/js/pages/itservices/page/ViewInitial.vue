<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ArrowLeft } from 'lucide-vue-next'
import { ITService, JobOrder } from '../types/types'

interface Props {
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
</script>

<template>
  <AppLayout title="Initial IT Service Details">
    <div
      class="min-h-screen from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800"
    >
      <div
        class="container mx-auto px-3 py-4 sm:px-4 sm:py-6 md:px-6 lg:px-8 lg:py-8"
      >
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="flex items-center gap-3">
              <button
                type="button"
                @click="goBack"
                class="rounded-lg p-2 transition hover:bg-gray-200 dark:hover:bg-gray-700"
                title="Go Back"
              >
                <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
              </button>
              <div>
                <h1
                  class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl"
                >
                  Initial Service Request
                </h1>
                <p
                  class="mt-1 text-sm text-gray-600 dark:text-gray-400 sm:text-base"
                >
                  Job Order Details & Machine Information
                </p>
              </div>
            </div>
            <div
              class="rounded-full bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-100 sm:px-4 sm:text-sm"
            >
              Job #{{ jobOrder.id }}
            </div>
          </div>
        </div>

        <div class="grid gap-6 sm:gap-8">
          <!-- Job Order Information Card -->
          <div
            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
          >
            <div
              class="bg-gradient-to-r from-slate-600 to-slate-700 px-4 py-4 dark:from-slate-700 dark:to-slate-800 sm:px-6"
            >
              <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-white/20 p-2 dark:bg-white/10">
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
                <div>
                  <h2 class="text-lg font-semibold text-white sm:text-xl">
                    Job Order Information
                  </h2>
                  <p
                    class="text-xs text-blue-100 dark:text-slate-200 sm:text-sm"
                  >
                    Client & Contact Details
                  </p>
                </div>
              </div>
            </div>
            <div class="p-4 sm:p-6">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Client / Company</label
                  >
                  <p
                    class="rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
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
                    class="rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
                  >
                    {{ jobOrder.contact_person }}
                  </p>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Contact Number</label
                  >
                  <div
                    class="flex items-center space-x-2 rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                  >
                    <svg
                      class="h-5 w-5 text-green-600 dark:text-green-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                      ></path>
                    </svg>
                    <p
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ jobOrder.contact_no }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Department</label
                  >
                  <p
                    class="rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
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
                      class="h-5 w-5 text-blue-600 dark:text-blue-400"
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
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ full_name(itService.technician) }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Service Date</label
                  >
                  <div
                    class="flex items-center space-x-2 rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                  >
                    <svg
                      class="h-5 w-5 text-gray-600 dark:text-gray-400"
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
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ formatDate(jobOrder.date_time) ?? 'N/A' }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2 sm:col-span-2">
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
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                      ></path>
                    </svg>
                    <p
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ jobOrder.address }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Machine Details Card -->
          <div
            class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
          >
            <div
              class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-4 dark:from-purple-700 dark:to-purple-800 sm:px-6"
            >
              <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-white/20 p-2 dark:bg-white/10">
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
                <div>
                  <h2 class="text-lg font-semibold text-white sm:text-xl">
                    Machine Information
                  </h2>
                  <p
                    class="text-xs text-purple-100 dark:text-purple-200 sm:text-sm"
                  >
                    Equipment Details & Problem Description
                  </p>
                </div>
              </div>
            </div>
            <div class="p-4 sm:p-6">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                <div class="space-y-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Machine Type</label
                  >
                  <div
                    class="flex items-center space-x-2 rounded-lg border border-purple-100 bg-purple-50 p-3 dark:border-purple-800 dark:bg-purple-900/30"
                  >
                    <svg
                      class="h-5 w-5 text-purple-600 dark:text-purple-400"
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
                    <p
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
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
                    class="rounded-lg bg-gray-50 p-3 text-base font-semibold text-gray-900 dark:bg-gray-700 dark:text-white sm:text-lg"
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
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
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
                      class="text-base font-semibold text-gray-900 dark:text-white sm:text-lg"
                    >
                      {{ itService.tag_no }}
                    </p>
                  </div>
                </div>
                <div class="space-y-2 sm:col-span-2">
                  <label
                    class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:text-sm"
                    >Machine Problem</label
                  >
                  <div
                    class="rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/30"
                  >
                    <div class="flex items-start space-x-2">
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
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                        ></path>
                      </svg>
                      <p
                        class="whitespace-pre-line text-sm leading-relaxed text-gray-900 dark:text-gray-100 sm:text-base"
                      >
                        {{ itService.machine_problem }}
                      </p>
                    </div>
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
