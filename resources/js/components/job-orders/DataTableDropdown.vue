<script setup lang="ts">
import { MoreVertical } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem,
    DropdownMenuSeparator, 
    DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
import { JobOrder } from '@/types'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  jobOrder: JobOrder
}>()

const { jobOrder } = props
  
const handlerRowArchival = () => {
  router.delete(route('job_order.destroy', jobOrder.id), {
    replace: true,
    onBefore: () => confirm(`Are you sure you want to archive ${jobOrder.client}`)
  })
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreVertical class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem>
        View
      </DropdownMenuItem>
      <DropdownMenuItem>
        Edit
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="handlerRowArchival">
        Archive
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>