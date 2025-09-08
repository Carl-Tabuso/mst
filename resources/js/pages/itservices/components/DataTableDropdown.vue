<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import { MoreVertical } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps<{ jobOrder: JobOrder }>()

const actions = computed(() => {
  const status = props.jobOrder.status?.toLowerCase()

  if (status === 'for check up') {
    return ['Proceed to First Onsite']
  } else if (status === 'for final service') {
    return ['Proceed to Final Onsite']
  }

  return ['']
})

function handleAction(action: string) {
  switch (action) {
    case 'Proceed to First Onsite':
      router.visit(
        route('job_order.it_service.onsite.initial.create', {
          iTService: props.jobOrder.serviceableId,
        }),
      )
      break

    case 'Proceed to Final Onsite':
      router.visit(
        route('job_order.it_service.onsite.final.create', {
          iTService: props.jobOrder.serviceableId,
        }),
      )
      break
  }
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button
        variant="ghost"
        class="h-8 w-8 p-0"
      >
        <span class="sr-only">Open menu</span>
        <MoreVertical class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem
        v-for="action in actions"
        :key="action"
        @click="handleAction(action)"
      >
        {{ action }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
