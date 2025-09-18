<script setup lang="ts">
import { Cpu, HardDrive, Monitor } from 'lucide-vue-next'

interface ITService {
  id: number
  machine_type: string
  model: string
}

defineProps<{
  services: ITService[]
}>()

const getServiceIcon = (machineType: string) => {
  const type = machineType.toLowerCase()
  if (
    type.includes('computer') ||
    type.includes('pc') ||
    type.includes('laptop')
  ) {
    return Monitor
  } else if (type.includes('server') || type.includes('cpu')) {
    return Cpu
  } else {
    return HardDrive
  }
}

const getServiceColor = (machineType: string) => {
  const type = machineType.toLowerCase()
  if (
    type.includes('computer') ||
    type.includes('pc') ||
    type.includes('laptop')
  ) {
    return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
  } else if (type.includes('server') || type.includes('cpu')) {
    return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
  } else {
    return 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
  }
}
</script>

<template>
  <div class="rounded-lg p-3 shadow-sm dark:bg-gray-900 sm:p-4 lg:p-6">
    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-sky-700">
      IT Services Handled
      <span class="text-base font-normal text-gray-500 dark:text-gray-400"
        >({{ services.length }})</span
      >
    </h3>

    <div
      v-if="services.length"
      class="space-y-3"
    >
      <div
        v-for="service in services"
        :key="service.id"
        class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
      >
        <div
          class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg"
          :class="getServiceColor(service.machine_type)"
        >
          <component
            :is="getServiceIcon(service.machine_type)"
            class="h-5 w-5"
          />
        </div>

        <div class="min-w-0 flex-1">
          <div class="truncate font-medium text-gray-900 dark:text-sky-700">
            {{ service.machine_type }}
          </div>
          <div class="truncate text-sm text-gray-500 dark:text-gray-400">
            {{ service.model }}
          </div>
        </div>
      </div>
    </div>

    <div
      v-else
      class="py-6 text-center text-gray-400 dark:text-gray-500 sm:py-8"
    >
      <div
        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
      >
        <Monitor class="h-8 w-8 text-gray-400 dark:text-gray-600" />
      </div>
      <p class="text-sm">No IT services handled yet.</p>
    </div>
  </div>
</template>
