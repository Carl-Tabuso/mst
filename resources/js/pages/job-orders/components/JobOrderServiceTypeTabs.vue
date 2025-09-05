<script setup lang="ts">
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <Tabs
    :model-value="route().current()"
    class="w-full"
  >
    <TabsList class="flex justify-start">
      <Link :href="route('job_order.index')">
        <TabsTrigger
          value="job_order.index"
          class="px-7"
        >
          All
        </TabsTrigger>
      </Link>
      <Link :href="route('job_order.waste_management.index')">
        <TabsTrigger
          value="job_order.waste_management.index"
          class="px-7"
        >
          Waste Management
        </TabsTrigger>
      </Link>
      <Link :href="route('job_order.it_service.index')">
        <TabsTrigger
          value="job_order.it_service"
          class="px-7"
        >
          IT Services
        </TabsTrigger>
      </Link>
      <Link :href="route('job_order.other_services.index')">
        <TabsTrigger
          value="job_order.other_services.index"
          class="px-7"
        >
          Other Services
        </TabsTrigger>
      </Link>
    </TabsList>
  </Tabs>
</template>
