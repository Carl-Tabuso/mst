<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { getInitials } from '@/composables/useInitials'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { JobOrder } from '@/types'
import { compareDesc, format } from 'date-fns'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'

interface TicketHeaderProps {
  jobOrder: JobOrder
}

const props = defineProps<TicketHeaderProps>()

const jobOrderStatus = computed(() => {
  return JobOrderStatuses.find((status) => {
    return props.jobOrder.status === status.id
  })
})

const createdAt = computed(() => new Date(props.jobOrder.createdAt))
const updatedAt = computed(() => new Date(props.jobOrder.updatedAt))

const isJobOrderUpdated = computed(() => {
  return compareDesc(createdAt.value, updatedAt.value)
})
</script>

<template>
  <div class="flex flex-col gap-1">
    <div class="flex items-center gap-4">
      <h3 class="scroll-m-20 text-3xl font-bold text-primary">
        Ticket:
        <span class="tracking-tighter text-muted-foreground">
          {{ jobOrder.ticket }}
        </span>
      </h3>
      <Dialog v-if="jobOrder.cancel">
        <DialogTrigger>
          <Button
            variant="ghost"
            class="rounded-full p-1"
          >
            <Badge
              :variant="jobOrderStatus?.badge"
              class="overflow-hidden truncate text-ellipsis"
            >
              {{ jobOrderStatus?.label }}
            </Badge>
          </Button>
        </DialogTrigger>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              <span class="font-bold text-destructive">
                {{ jobOrderStatus?.label }}
              </span>
            </DialogTitle>
            <DialogDescription>
              <!---->
            </DialogDescription>
          </DialogHeader>
          <div>
            <Label> Reason: </Label>
            <div class="rounded-md border py-3">
              <div class="mx-4 text-sm leading-4 text-muted-foreground">
                {{ jobOrder.cancel.reason }}
              </div>
            </div>
          </div>
        </DialogContent>
      </Dialog>
      <template v-else>
        <Badge
          :variant="jobOrderStatus?.badge"
          class="overflow-hidden truncate text-ellipsis"
        >
          {{ jobOrderStatus?.label }}
        </Badge>
      </template>
    </div>
    <div class="flex items-center gap-2">
      <Avatar class="h-7 w-7 shrink-0 rounded-full">
        <AvatarImage
          v-if="jobOrder.creator?.account?.avatar"
          :src="jobOrder.creator.account.avatar"
          :alt="jobOrder.creator.fullName"
        />
        <AvatarFallback>
          {{ getInitials(jobOrder.creator?.fullName) }}
        </AvatarFallback>
      </Avatar>
      <div class="flex items-center gap-3 text-sm text-muted-foreground">
        <span>{{ `${jobOrder.creator?.fullName}` }}</span>
        <span>•</span>
        <div class="flex items-center gap-1">
          <Calendar
            :size="16"
            class="mr-1"
          />
          <span>{{
            `${format(updatedAt, "EEEE, MMMM d, yyyy 'at' h:mm a")}
            ${isJobOrderUpdated ? '(Edited)' : ''}`
          }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
