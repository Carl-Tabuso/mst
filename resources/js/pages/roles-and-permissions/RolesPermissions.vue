<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import {
  ArchiveIcon,
  CheckIcon,
  ClipboardListIcon,
  ClockIcon,
  GaugeCircleIcon,
  KeyIcon,
  ShieldAlertIcon,
  UsersIcon,
} from 'lucide-vue-next'

const roles = [
  'Head Frontliner',
  'Frontliner',
  'Dispatcher',
  'Team Lead',
  'Consultant',
  'Human Resource',
  'IT Admin',
  'Employee',
]

const permissions = [
  {
    group: 'Job Order',
    icon: ClipboardListIcon,
    items: [
      {
        name: 'Create, View, and Edit Job Order',
        roles: [true, true, false, false, false, false, false, false],
      },
    ],
  },
  {
    group: 'Incident Report',
    icon: ShieldAlertIcon,
    items: [
      {
        name: 'File incident report if there are any accidents occurred',
        roles: [false, false, false, true, false, true, false, true],
      },
      {
        name: 'Verify Incident Report filed',
        roles: [false, false, false, false, false, true, false, false],
      },
    ],
  },
  {
    group: 'Performance Monitoring',
    icon: GaugeCircleIcon,
    items: [
      {
        name: 'Evaluate team members after hauling',
        roles: [false, false, false, true, false, false, false, true],
      },
      {
        name: "Track and View Team Lead and Employees' evaluation",
        roles: [false, false, false, false, true, false, false, false],
      },
    ],
  },
  {
    group: 'Data and Analytics',
    icon: GaugeCircleIcon,
    items: [
      {
        name: 'Displays overview of Job Order, Evaluation, and Report statistics',
        roles: [true, true, false, false, false, true, false, false],
      },
    ],
  },
  {
    group: 'Employee Management',
    icon: UsersIcon,
    items: [
      {
        name: 'Create, View, and Edit Employee information',
        roles: [true, false, false, false, false, false, false, false],
      },
    ],
  },
  {
    group: 'User Management',
    icon: KeyIcon,
    items: [
      {
        name: 'Create, View, and Edit User Accounts for Employee Management',
        roles: [false, false, false, false, false, false, true, false],
      },
    ],
  },
  {
    group: 'Archived',
    icon: ArchiveIcon,
    items: [
      {
        name: 'Keeps inactive records accessible. Allows restoring them to active use',
        roles: [false, false, false, false, false, false, true, false],
      },
    ],
  },
  {
    group: 'Activity Logs',
    icon: ClockIcon,
    items: [
      {
        name: 'Logs user and system activity for tracking and review',
        roles: [false, false, false, false, false, false, true, false],
      },
    ],
  },
]
</script>

<template>
  <AppLayout>
    <div class="max-w-full overflow-x-auto px-6 py-10">
      <h1 class="mb-2 text-3xl font-bold">Roles and Permissions</h1>
      <p class="mb-6 max-w-4xl text-muted-foreground">
        A reference guide for system-defined user roles and the permissions
        assigned to each.
      </p>

      <div class="w-full min-w-[1024px] rounded-md border">
        <div
          class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] border-b bg-muted px-4 py-3 text-sm font-medium"
        >
          <div class="text-left">Permissions</div>
          <div
            v-for="role in roles"
            :key="role"
            class="text-center text-muted-foreground"
          >
            {{ role }}
          </div>
        </div>

        <div
          v-for="section in permissions"
          :key="section.group"
        >
          <div
            class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] items-center border-b bg-blue-100 px-4 py-2 font-semibold text-blue-800"
          >
            <div class="flex items-center gap-2">
              <component
                :is="section.icon"
                class="h-4 w-4 text-blue-700"
              />
              <span class="text-sm">{{ section.group }}</span>
            </div>
            <div
              v-for="i in 8"
              :key="i"
            ></div>
          </div>

          <div
            v-for="item in section.items"
            :key="item.name"
            class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] items-center border-b px-4 py-3 text-sm hover:bg-muted/50"
          >
            <div class="text-muted-foreground">{{ item.name }}</div>
            <div
              v-for="(hasPermission, i) in item.roles"
              :key="i"
              class="flex justify-center"
            >
              <div
                class="flex h-5 w-5 items-center justify-center rounded border"
                :class="
                  hasPermission
                    ? 'border-blue-800 bg-blue-800 text-white'
                    : 'border-gray-300 bg-white'
                "
              >
                <CheckIcon
                  v-if="hasPermission"
                  class="h-4 w-4"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
