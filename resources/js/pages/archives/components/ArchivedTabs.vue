<script setup lang="ts">
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { usePermissions } from '@/composables/usePermissions'
import { Link } from '@inertiajs/vue3'
import { ClipboardList, UserRoundCog, UsersRound } from 'lucide-vue-next'
import { FunctionalComponent } from 'vue'

interface Tab {
  label: string
  value: string
  icon: FunctionalComponent
  can?: boolean
}

const { canAny } = usePermissions()

const tabs: Tab[] = [
  {
    label: 'Job Orders',
    value: 'archive.job_order.index',
    icon: ClipboardList,
    can: canAny({ roles: ['head frontliner', 'frontliner'] }),
  },
  {
    label: 'Employees',
    value: 'archive.employee.index',
    icon: UsersRound,
    can: canAny({ roles: ['head frontliner', 'human resource'] }),
  },
  {
    label: 'Users',
    value: 'archive.user.index',
    icon: UserRoundCog,
    can: canAny({ roles: ['head frontliner', 'it admin'] }),
  },
]
</script>

<template>
  <Tabs
    :model-value="route().current()"
    class="w-full"
  >
    <TabsList class="flex flex-row justify-start">
      <template
        v-for="(tab, index) in tabs"
        :key="index"
      >
        <Link
          :href="route(tab.value)"
          :class="{ 'pointer-events-none opacity-80': !tab.can }"
        >
          <TabsTrigger :value="tab.value">
            <div class="flex flex-row items-center px-3">
              <component
                :is="tab.icon"
                class="mr-4 h-4 w-4"
              />
              <span>{{ tab.label }}</span>
            </div>
          </TabsTrigger>
        </Link>
      </template>
    </TabsList>
  </Tabs>
</template>
