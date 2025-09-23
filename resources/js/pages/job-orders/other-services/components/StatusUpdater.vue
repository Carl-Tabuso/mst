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
  DialogTrigger,
} from '@/components/ui/dialog'
import { Textarea } from '@/components/ui/textarea'
import { useForm } from '@inertiajs/vue3'
import {
  Archive,
  CheckCircle,
  CircleArrowRight,
  LoaderCircle,
} from 'lucide-vue-next'
import { ref, useTemplateRef } from 'vue'
import { toast } from 'vue-sonner'

interface Form5StatusUpdaterProps {
  status: string
  ticket: string
}

const { ticket } = defineProps<Form5StatusUpdaterProps>()

const reasonInput = useTemplateRef('reasonInput')
const reason = ref<string>('')
const isOpen = ref<boolean>(false)

const form = useForm<Record<string, string>>({})

const statusOptions = {
  completed: {
    tag: 'completed',
    label: 'Mark as Completed',
    description: 'This will mark the Form5 service as successfully completed.',
    icon: CheckCircle,
    variant: 'default' as const,
  },
  closed: {
    tag: 'closed',
    label: 'Mark as Closed',
    description: 'This will permanently close the Form5 service ticket.',
    icon: Archive,
    variant: 'destructive' as const,
  },
}

const onMarkAsCompleted = () => {
  form
    .transform(() => ({
      status: statusOptions.completed.tag,
    }))
    .patch(route('job_order.update', ticket), {
      preserveScroll: true,
      preserveState: false,
      showProgress: false,
      onSuccess: () => {
        isOpen.value = false
        toast.success('Job order marked as completed', {
          position: 'top-center',
        })
      },
      onError: (errors) => {
        if (Object.keys(errors).length > 0) {
          Object.values(errors).forEach((error) => {
            toast.error(error, {
              position: 'top-center',
            })
          })
        }
      },
    })
}

const onMarkAsClosed = () => {
  form
    .transform(() => ({
      status: statusOptions.closed.tag,
      reason: reason.value,
    }))
    .post(route('job_order.cancel.create', ticket), {
      preserveScroll: true,
      showProgress: false,
      onError: () => reasonInput.value?.$el.focus(),
      onSuccess: () => {
        isOpen.value = false
        toast.success('Job Order marked as closed', {
          position: 'top-center',
        })
      },
    })
}
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
        <DialogTitle>Form5 Service Status Update</DialogTitle>
        <DialogDescription>
          Update the status of this Form5 service request.
        </DialogDescription>
      </DialogHeader>

      <div class="mt-4 space-y-4">
        <!-- Mark as Completed Option -->
        <div class="flex items-center justify-between rounded-lg border p-4">
          <div class="flex-1">
            <h4 class="font-medium">{{ statusOptions.completed.label }}</h4>
            <p class="mt-1 text-sm text-muted-foreground">
              {{ statusOptions.completed.description }}
            </p>
          </div>
          <Button
            :variant="statusOptions.completed.variant"
            :disabled="form.processing"
            @click="onMarkAsCompleted"
            class="ml-4"
          >
            <CheckCircle class="mr-2 h-4 w-4" />
            Complete
          </Button>
        </div>

        <!-- Mark as Closed Option -->
        <Dialog>
          <DialogTrigger as-child>
            <div
              class="flex cursor-pointer items-center justify-between rounded-lg border p-4 hover:bg-muted/50"
            >
              <div class="flex-1">
                <h4 class="font-medium">{{ statusOptions.closed.label }}</h4>
                <p class="mt-1 text-sm text-muted-foreground">
                  {{ statusOptions.closed.description }}
                </p>
              </div>
              <Button
                :variant="statusOptions.closed.variant"
                class="ml-4"
              >
                <Archive class="mr-2 h-4 w-4" />
                Close
              </Button>
            </div>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Close Form5 Service</DialogTitle>
              <DialogDescription>
                Please provide a reason for closing this Form5 service ticket.
              </DialogDescription>
            </DialogHeader>
            <Textarea
              ref="reasonInput"
              v-model="reason"
              id="reason"
              required
              placeholder="Enter the reason for closing this ticket..."
              :class="[
                'h-[150px] w-full',
                {
                  'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                    form.errors.reason,
                },
              ]"
            />
            <div class="text-sm text-muted-foreground">
              This action cannot be undone. The ticket will be permanently
              closed.
            </div>
            <DialogFooter class="mt-5 flex items-center gap-3">
              <DialogClose>
                <Button
                  variant="outline"
                  type="button"
                  :disabled="form.processing"
                >
                  Cancel
                </Button>
              </DialogClose>
              <Button
                variant="destructive"
                type="button"
                :disabled="form.processing || !reason.trim()"
                @click="onMarkAsClosed"
              >
                <LoaderCircle
                  v-show="form.processing"
                  class="mr-2 h-4 w-4 animate-spin"
                />
                Confirm Close
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      <DialogFooter class="mt-6">
        <DialogClose>
          <Button
            variant="outline"
            type="button"
          >
            Cancel
          </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
