<script setup lang="ts">
import { Monitor, Cpu, HardDrive } from 'lucide-vue-next'

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
  if (type.includes('computer') || type.includes('pc') || type.includes('laptop')) {
    return Monitor
  } else if (type.includes('server') || type.includes('cpu')) {
    return Cpu
  } else {
    return HardDrive
  }
}

const getServiceColor = (machineType: string) => {
  const type = machineType.toLowerCase()
  if (type.includes('computer') || type.includes('pc') || type.includes('laptop')) {
    return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
  } else if (type.includes('server') || type.includes('cpu')) {
    return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
  } else {
    return 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
  }
}
</script>

<template>
  <div class="dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-sky-700 mb-4">
      IT Services Handled
      <span class="text-base font-normal text-gray-500 dark:text-gray-400">({{ services.length }})</span>
    </h3>

    <div v-if="services.length" class="space-y-3">
      <div v-for="service in services" :key="service.id"
        class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
          :class="getServiceColor(service.machine_type)">
          <component :is="getServiceIcon(service.machine_type)" class="w-5 h-5" />
        </div>
        <div>
          <div class="font-medium text-gray-900 dark:text-sky-700">{{ service.machine_type }}</div>
          <div class="text-sm text-gray-500 dark:text-gray-400">{{ service.model }}</div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-8 text-gray-400 dark:text-gray-500">
      No IT services handled yet.
    </div>
  </div>
</template>