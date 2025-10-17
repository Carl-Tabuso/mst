<script setup lang="ts">
import MainContainer from '@/components/MainContainer.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import {
  AwardIcon,
  CheckIcon,
  ClipboardListIcon,
  ClockIcon,
  FilePenLineIcon,
  GaugeCircleIcon,
  LayoutDashboardIcon,
  Truck,
  UserRoundCog,
  UsersRoundIcon,
  X,
} from 'lucide-vue-next'

interface Props {
  roles: {
    [key: string]: string[]
  }
  permissionGroups: {
    group: string
    icon: string
    permissions: string[]
  }[]
  permissionLabels: {
    [key: string]: string
  }
}

const props = defineProps<Props>()

const roleNames = Object.keys(props.roles)

const iconComponents: { [key: string]: any } = {
  ClipboardListIcon,
  AwardIcon,
  FilePenLineIcon,
  UsersRoundIcon,
  UserRoundCog,
  LayoutDashboardIcon,
  GaugeCircleIcon,
  Truck,
  ClockIcon,
}

const hasPermission = (role: string, permission: string): boolean => {
  return props.roles[role]?.includes(permission) || false
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    href: route('home'),
    title: 'Home',
  },
  {
    href: route('roles-permissions'),
    title: 'Roles and Permissions',
  },
]
</script>

<template>
  <Head title="Roles and Permissions" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <MainContainer>
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
            v-for="role in roleNames"
            :key="role"
            class="text-center text-muted-foreground"
          >
            {{ role }}
          </div>
        </div>

        <div
          v-for="group in permissionGroups"
          :key="group.group"
        >
          <div
            class="b grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] items-center border-b bg-blue-100 px-4 py-2 font-semibold text-blue-800 dark:bg-blue-900 dark:text-white"
          >
            <div class="flex items-center gap-2 text-foreground">
              <component
                :is="iconComponents[group.icon]"
                class="h-4 w-4"
              />
              <span class="text-sm">{{ group.group }}</span>
            </div>
            <div
              v-for="i in 8"
              :key="i"
            ></div>
          </div>

          <div
            v-for="permission in group.permissions"
            :key="permission"
            class="grid grid-cols-[256px_repeat(8,minmax(80px,1fr))] items-center border-b px-4 py-3 text-sm hover:bg-muted/50"
          >
            <div class="text-muted-foreground">
              {{ permissionLabels[permission] }}
            </div>
            <div
              v-for="role in roleNames"
              :key="role"
              class="flex justify-center"
            >
              <div class="size-3">
                <CheckIcon
                  v-if="hasPermission(role, permission)"
                  class="text-foreground"
                />
                <X
                  v-else
                  class="stroke-1 text-muted-foreground"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </MainContainer>
  </AppLayout>
</template>
