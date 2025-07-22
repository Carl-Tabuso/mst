<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { 
  Dialog, 
  DialogClose, 
  DialogContent, 
  DialogDescription, 
  DialogFooter, 
  DialogHeader, 
  DialogTitle, 
  DialogTrigger 
} from '@/components/ui/dialog'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { CircleArrowRight } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { Textarea } from '@/components/ui/textarea'

interface StatusUpdaterProps {
  status: JobOrderStatus
}

interface StatusUpdaterEmits {
  markAsUpdate: [next: JobOrderStatus]
  markAsStop: [stop: JobOrderStatus]
}

const props = defineProps<StatusUpdaterProps>()

defineEmits<StatusUpdaterEmits>()

interface manualStatusUpdaterType {
  id: JobOrderStatus
  next: {
    tag: JobOrderStatus
    label: string
  }
  stop: {
    tag: JobOrderStatus
    label: string
  }
  description?: string
}

const manualStatusUpdater: manualStatusUpdaterType[] = [
  {
    id: 'for viewing',
    next: {
      tag: 'for proposal',
      label: 'Mark as viewed'
    },
    stop: {
      tag: 'dropped',
      label: 'Mark as dropped'
    },
    description: 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad reprehenderit aperiam ullam mollitia repellendus dolor dolores vero, laudantium explicabo voluptatibus velit eaque ut odit nihil qui est reiciendis alias ipsam?'
  },
  {
    id: 'for proposal',
    next: {
      tag: 'successful',
      label: 'Mark as successful'
    },
    stop: {
      tag: 'failed',
      label: 'Mark as failed'
    },
    description: 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad reprehenderit aperiam ullam mollitia repellendus dolor dolores vero, laudantium explicabo voluptatibus velit eaque ut odit nihil qui est reiciendis alias ipsam?'
  },
]

const currentStatus = computed(() =>
  manualStatusUpdater.find((msu) => msu.id === props.status),
)

const reason = ref<string>('')

const isOpen = ref<boolean>(false)

defineExpose({ reason, isOpen })
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger>
      <Button type="button">
        <CircleArrowRight class="mr-2" />
        Update Status
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>
          Read Carefully!
        </DialogTitle>
      </DialogHeader>
      <div class="text-muted-foreground text-sm">
        {{ currentStatus?.description }}
      </div>
      <DialogFooter class="mt-5">
        <div class="flex items-center gap-3">
          <Dialog>
            <DialogTrigger>
              <Button
                type="button"
                variant="destructive"
                class="font-normal"
              >
                {{ currentStatus?.stop.label }}
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>
                  Why?
                </DialogTitle>
                <DialogDescription>
                  Please provide a reason before permanently closing this ticket.
                </DialogDescription>
              </DialogHeader>
              <Textarea
                v-model="reason"
                id="reason"
                required
                placeholder=""
                class="w-full h-[150px]"
              />
              <DialogFooter class="mt-5 flex items-center gap-3">
                <DialogClose>
                    <Button
                      variant="outline"
                      type="button"
                    >
                      Go back
                    </Button>
                </DialogClose>
                <Button
                  variant="destructive"
                  type="button"
                  @click="$emit('markAsStop', currentStatus!.stop.tag)"
                >
                  {{ currentStatus?.stop.label }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
          <Button
            type="button"
            @click="$emit('markAsUpdate', currentStatus!.next.tag)"
          >
            {{ currentStatus?.next.label }}
          </Button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
