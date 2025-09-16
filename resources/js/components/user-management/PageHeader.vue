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

const cannotCreateUser = cannot('create:user_account')
</script>

<template>
  <div class="mb-1 flex items-center justify-between">
    <div class="flex flex-col">
      <h3 class="scroll-m-20 text-3xl font-bold text-primary">
        {{ title }}
      </h3>
      <p class="text-muted-foreground">
        {{ subTitle }}
      </p>
    </div>
      <Button @click="$emit('create')">
      <Plus class="mr-2 h-4 w-4" />
      Create User
    </Button>
  </div>
</template>