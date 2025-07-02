<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import {
  CheckIcon,
  ClipboardListIcon,
  ShieldAlertIcon,
  GaugeCircleIcon,
  UsersIcon,
  KeyIcon,
  ArchiveIcon,
  ClockIcon
} from 'lucide-vue-next'

const roles = [
  'Head Frontliner',
  'Frontliner',
  'Dispatcher',
  'Team Lead',
  'Consultant',
  'Human Resource',
  'IT Admin',
  'Employee'
]

const permissions = [
  {
    group: 'Job Order',
    icon: ClipboardListIcon,
    items: [
      { name: 'Create, View, and Edit Job Order', roles: [true, true, false, false, false, false, false, false] }
    ]
  },
  {
    group: 'Incident Report',
    icon: ShieldAlertIcon,
    items: [
      { name: 'File incident report if there are any accidents occurred', roles: [false, false, false, true, false, true, false, true] },
      { name: 'Verify Incident Report filed', roles: [false, false, false, false, false, true, false, false] }
    ]
  },
  {
    group: 'Performance Monitoring',
    icon: GaugeCircleIcon,
    items: [
      { name: 'Evaluate team members after hauling', roles: [false, false, false, true, false, false, false, true] },
      { name: "Track and View Team Lead and Employees' evaluation", roles: [false, false, false, false, true, false, false, false] }
    ]
  },
  {
    group: 'Data and Analytics',
    icon: GaugeCircleIcon,
    items: [
      { name: 'Displays overview of Job Order, Evaluation, and Report statistics', roles: [true, true, false, false, false, true, false, false] }
    ]
  },
  {
    group: 'Employee Management',
    icon: UsersIcon,
    items: [
      { name: 'Create, View, and Edit Employee information', roles: [true, false, false, false, false, false, false, false] }
    ]
  },
  {
    group: 'User Management',
    icon: KeyIcon,
    items: [
      { name: 'Create, View, and Edit User Accounts for Employee Management', roles: [false, false, false, false, false, false, true, false] }
    ]
  },
  {
    group: 'Archived',
    icon: ArchiveIcon,
    items: [
      { name: 'Keeps inactive records accessible. Allows restoring them to active use', roles: [false, false, false, false, false, false, true, false] }
    ]
  },
  {
    group: 'Activity Logs',
    icon: ClockIcon,
    items: [
      { name: 'Logs user and system activity for tracking and review', roles: [false, false, false, false, false, false, true, false] }
    ]
  }
]
</script>

<template>
  <AppLayout>
    <div class="py-10 px-6 max-w-full overflow-x-auto">
      <h1 class="text-3xl font-bold mb-2">Roles and Permissions</h1>
      <p class="text-muted-foreground mb-6 max-w-4xl">
        A reference guide for system-defined user roles and the permissions assigned to each.
      </p>

      <div class="rounded-md border w-full min-w-[1024px]">
        <div class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] bg-muted text-sm font-medium border-b px-4 py-3">
          <div class="text-left">Permissions</div>
          <div v-for="role in roles" :key="role" class="text-center text-muted-foreground">
            {{ role }}
          </div>
        </div>

        <div v-for="section in permissions" :key="section.group">
          <div class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] bg-blue-100 text-blue-800 font-semibold px-4 py-2 border-b items-center">
            <div class="flex items-center gap-2">
              <component :is="section.icon" class="w-4 h-4 text-blue-700" />
              <span class="text-sm">{{ section.group }}</span>
            </div>
            <div v-for="i in 8" :key="i"></div>
          </div>

          <div
            v-for="item in section.items"
            :key="item.name"
            class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] items-center px-4 py-3 border-b text-sm hover:bg-muted/50"
          >
            <div class="text-muted-foreground">{{ item.name }}</div>
            <div
              v-for="(hasPermission, i) in item.roles"
              :key="i"
              class="flex justify-center"
            >
              <div
                class="w-5 h-5 rounded border flex items-center justify-center"
                :class="hasPermission ? 'bg-blue-800 border-blue-800 text-white' : 'bg-white border-gray-300'"
              >
                <CheckIcon v-if="hasPermission" class="w-4 h-4" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
