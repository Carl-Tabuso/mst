<script setup lang="ts">
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
import UserAvatar from '@/components/UserAvatar.vue'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { JobOrder } from '@/types'
import { compareDesc, format, formatDistanceToNow } from 'date-fns'
import { Calendar } from 'lucide-vue-next'
import { computed } from 'vue'

interface TicketHeaderProps {
  jobOrder: JobOrder
}

const props = defineProps<TicketHeaderProps>()

const { statusMap } = useJobOrderDicts()

const status = statusMap[props.jobOrder.status]

const createdAt = computed(() => new Date(props.jobOrder.createdAt))
const updatedAt = computed(() => new Date(props.jobOrder.updatedAt))

const isJobOrderUpdated = computed(() => {
  return compareDesc(createdAt.value, updatedAt.value)
})
</script>

<template>
  <div class="flex flex-col gap-2">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-4">
      <h3 class="whitespace-nowrap text-3xl font-bold text-primary sm:text-3xl">
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
              :variant="status.badge"
              class="w-fit"
            >
              {{ status.label }}
            </Badge>
          </Button>
        </DialogTrigger>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              <span class="font-bold text-destructive">
                {{ status.label }}
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
          :variant="status.badge"
          class="w-fit"
        >
          {{ status.label }}
        </Badge>
      </template>
    </div>
    <div class="flex flex-row items-center gap-2">
      <UserAvatar
        :avatar-path="jobOrder.creator?.account?.avatar"
        :fallback="jobOrder.creator.fullName"
      />
      <div
        class="flex items-center gap-3 overflow-y-auto whitespace-nowrap text-xs text-muted-foreground sm:text-sm"
      >
        <span>{{ `${jobOrder.creator?.fullName}` }}</span>
        <span>•</span>
        <div class="flex items-center gap-1">
          <Calendar
            :size="16"
            class="mr-1"
          />
          <span class="hidden sm:flex">{{
            `${format(updatedAt, "EEEE, MMMM d, yyyy 'at' h:mm a")}`
          }}</span>
          <span class="flex sm:hidden">
            {{ `${formatDistanceToNow(updatedAt, { addSuffix: true })}` }}
          </span>
          {{ isJobOrderUpdated ? '(Edited)' : '' }}
        </div>
      </div>
    </div>
  </div>
</template>
