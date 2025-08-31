<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog'
import { Truck } from '@/types'
import { formatDistanceToNow, isEqual } from 'date-fns'
import { computed, ref } from 'vue'
import EditTruckDetails from './EditTruckDetails.vue'

interface TruckCardProps {
  truck: Truck
}

const props = defineProps<TruckCardProps>()

const isDialogOpen = ref<boolean>(false)

const additionalInfo = computed(() => {
  const firstDate = new Date(props.truck.createdAt)
  const secondDate = new Date(props.truck.updatedAt)
  const creator = props.truck.creator?.fullName ?? 'Unknown Employee'

  if (isEqual(firstDate, secondDate)) {
    return `Added by ${creator} ${formatDistanceToNow(firstDate)} ago`
  }
  return `Updated by ${creator} ${formatDistanceToNow(secondDate)} ago`
})
</script>

<template>
  <Dialog v-model:open="isDialogOpen">
    <DialogTrigger as-child>
      <button>
        <Card
          class="flex flex-row items-center justify-between rounded-md border px-4 py-3 hover:bg-muted"
        >
          <CardContent class="flex items-center gap-4 p-0">
            <div class="flex flex-col gap-1">
              <div class="flex flex-row items-center gap-2">
                <span class="font-medium text-foreground">
                  {{ truck.plateNo }}
                </span>
                <span class="text-sm font-semibold text-muted-foreground">
                  {{ truck.model }}
                </span>
              </div>
              <span class="text-left text-xs text-muted-foreground">
                {{ additionalInfo }}
              </span>
            </div>
          </CardContent>
        </Card>
      </button>
    </DialogTrigger>
    <DialogContent>
      <EditTruckDetails
        :truck="truck"
        :additional-info="additionalInfo"
        @on-truck-details-update="isDialogOpen = false"
      />
    </DialogContent>
  </Dialog>
</template>
