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

const { can } = usePermissions()
</script>

<template>
  <div class="mb-1 flex items-center">
    <div class="flex flex-col">
      <h3 class="scroll-m-20 text-3xl font-bold text-primary">
        {{ title }}
      </h3>
      <p class="text-muted-foreground">
        {{ subTitle }}
      </p>
    </div>
    <div
      v-if="can('create:job_order')"
      class="ml-auto"
    >
      <Link
        :href="route('job_order.create')"
        preserve-state
      >
        <Button variant="default">
          <Plus class="mr-2" />
          Create Job Order
        </Button>
      </Link>
    </div>
  </div>
</template>
