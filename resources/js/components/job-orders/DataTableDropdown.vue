<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'
import { MoreVertical } from 'lucide-vue-next'

const props = defineProps<{
  jobOrder: JobOrder
}>()

const { jobOrder } = props

const handlerRowArchival = () => {
  router.delete(route('job_order.destroy', jobOrder.id), {
    replace: true,
    onBefore: () =>
      confirm(`Are you sure you want to archive ${jobOrder.client}`),
  })
}

const handleRowView = () => {
  //
}

const handleRowEdit = () => {
  const path = jobOrderRouteNames.find((j) => j.id === jobOrder.serviceableType)

  router.visit(route(`job_order.${path?.route}.edit`, jobOrder.serviceableId), {
    preserveState: true,
    // only: ['form4']
  })
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="h-8 w-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreVertical class="h-4 w-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem @click="handleRowView"> View </DropdownMenuItem>
      <DropdownMenuItem @click="handleRowEdit"> Edit </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="handlerRowArchival"> Archive </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
