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

defineProps<{
  jobOrder: JobOrder
}>()

function copy(id: string) {
  navigator.clipboard.writeText(id)
}

const goToRating = () => {
  router.get(route('employee.ratings.create'), {
    job_order_id: props.jobOrder.id,
  })
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
      <!-- <DropdownMenuLabel>Actions</DropdownMenuLabel> -->
      <!-- <DropdownMenuItem @click="copy(payment.id)">
        Copy Job Order ID
      </DropdownMenuItem> -->
      <!-- <DropdownMenuSeparator /> -->
      <DropdownMenuItem>View</DropdownMenuItem>
      <DropdownMenuItem>Edit</DropdownMenuItem>
      <DropdownMenuItem>Delete</DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
