<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { JobOrder } from '@/types'
import { MoreVertical } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

const props = defineProps<{ jobOrder: JobOrder }>()

const actions = computed(() => {
  const status = props.jobOrder.status?.toLowerCase()

  if (status === 'for check up') {
    return ['Proceed to First Onsite', 'Edit Request']
  } else if (status === 'for final service') {
    return ['Proceed to Final Onsite', 'Edit Request']
  } else if (status === 'completed') {
    return ['Edit Request']
  }

  return ['']

  // if (status === 'for check up') {
  //   return ['Proceed to First Onsite', 'Edit Request', 'View']
  // } else if (status === 'for final service') {
  //   return ['Proceed to Final Onsite', 'Edit Request', 'View']
  // } else if (status === 'completed') {
  //   return ['Edit Request', 'View']
  // }

  // return ['View']
})

function handleAction(action: string) {
  const jobOrderId = props.jobOrder.id;
  const serviceableId = props.jobOrder.serviceableId;
  const status = props.jobOrder.status;
  const reportInitialId = props.jobOrder.serviceable?.reportInitial?.id;

  switch (action) {
    // case 'View':
    //   if (status === 'for check up') {
    //     router.visit(`/job-orders/it-services/${jobOrderId}`);
    //   } else if (status === 'for final service') {
    //     router.visit(`/job-orders/it-services/${jobOrderId}/onsite/initial/${reportInitialId}/view`);
    //   } else if (status === 'completed') {
    //     router.visit(`/job-orders/it-services/${jobOrderId}/onsite/final/view`);
    //   } else {
    //     router.visit(`/job-orders/it-services/${jobOrderId}`);
    //   }
    //   break;

    case 'Edit Request':
      if (status === 'for check up') {
        router.visit(`/job-orders/it-services/${jobOrderId}/edit`);
      } else if (status === 'for final service') {
        router.visit(`/job-orders/it-services/${jobOrderId}/onsite/initial/${reportInitialId}/edit`);
      } else if (status === 'completed') {
        router.visit(`/job-orders/it-services/${jobOrderId}/onsite/final/edit`);
      } else {
        router.visit(`/job-orders/it-services/${jobOrderId}/edit`);
      }
      break;

    case 'Proceed to First Onsite':
      router.visit(route('job_order.it_service.onsite.create', {
        itService: props.jobOrder.serviceableId,
      }));
      break;

    case 'Proceed to Final Onsite':
      router.visit(`/job-orders/it-services/${jobOrderId}/onsite/final?service_id=${serviceableId}`);
      break;
  }
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
      <DropdownMenuItem v-for="action in actions" :key="action" @click="handleAction(action)">
        {{ action }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
