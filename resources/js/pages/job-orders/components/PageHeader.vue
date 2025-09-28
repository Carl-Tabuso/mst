<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue'
import { usePermissions } from '@/composables/usePermissions'
import { Link } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'

interface PageHeaderProps {
  title: string
  subTitle: string
}

defineProps<PageHeaderProps>()

const { cannot } = usePermissions()

const cannotCreateJobOrder = cannot('create:job_order')
</script>

<template>
  <div class="mb-1 flex flex-col gap-2 sm:flex-row sm:items-center">
    <div class="flex flex-col">
      <h3 class="scroll-m-20 text-2xl font-bold text-primary sm:text-3xl">
        {{ title }}
      </h3>
      <p class="text-sm text-muted-foreground sm:text-base">
        {{ subTitle }}
      </p>
    </div>
    <Link
      :href="route('job_order.create')"
      preserve-state
      class="sm:ml-auto"
      :class="{ 'pointer-events-none': cannotCreateJobOrder }"
    >
      <Button
        variant="default"
        :disabled="cannotCreateJobOrder"
        class="w-full sm:w-auto"
      >
        <Plus class="mr-2" />
        Create Job Order
      </Button>
    </Link>
  </div>
</template>
