<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Label } from '@/components/ui/label'

// defineProps();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders'
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: 'Create',
    href: '#'
  }
]
</script>

<template>
    <Head title="Create Job Order" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="px-3 py-3">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
          <div class="flex items-center mb-3">
            <div class="flex flex-col gap-y-1">
              <h3 class="scroll-m-20 text-3xl font-bold">
                Add Job Order
              </h3>
              <RadioGroup class="flex items-center space-x-9">
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r1" value="default" />
                  <Label for="r1">Default</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r2" value="comfortable" />
                  <Label for="r2">Comfortable</Label>
                </div>
                <div class="flex items-center space-x-2">
                  <RadioGroupItem id="r3" value="compact" />
                  <Label for="r3">Compact</Label>
                </div>
              </RadioGroup>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
</template>
