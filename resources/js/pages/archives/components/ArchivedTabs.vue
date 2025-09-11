<script setup lang="ts">
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Link } from '@inertiajs/vue3'
import { ClipboardList, UserRoundCog, UsersRound } from 'lucide-vue-next'
import { FunctionalComponent } from 'vue'

interface Tab {
  label: string
  value: string
  icon: FunctionalComponent
}

const tabs: Tab[] = [
  {
    label: 'Job Orders',
    value: 'archive.job_order.index',
    icon: ClipboardList,
  },
  {
    label: 'Employees',
    value: 'archive.employee.index',
    icon: UsersRound,
  },
  {
    label: 'Users',
    value: 'archive.user.index',
    icon: UserRoundCog,
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
        v-for="({ label, value, icon }, index) in tabs"
        :key="index"
      >
        <Link :href="route(value)">
          <TabsTrigger :value="value">
            <div class="flex flex-row items-center px-3">
              <component
                :is="icon"
                class="mr-4 h-4 w-4"
              />
              <span>{{ label }}</span>
            </div>
          </TabsTrigger>
        </Link>
      </template>
    </TabsList>
  </Tabs>
</template>
