<script setup lang="ts">
import InputError from '@/components/InputError.vue'
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
import {
  JobOrderStatus,
  manualUpdateStatuses,
} from '@/constants/job-order-statuses'
import { useForm } from '@inertiajs/vue3'
import { CircleArrowRight, LoaderCircle } from 'lucide-vue-next'
import { computed, ref, useTemplateRef } from 'vue'
import { toast } from 'vue-sonner'

interface StatusUpdaterProps {
  status: JobOrderStatus
  ticket: string
}

const { status, ticket } = defineProps<StatusUpdaterProps>()

const currentStatus = computed(() => {
  return manualUpdateStatuses.find((msu) => msu.id === status)
})

const reasonInput = useTemplateRef('reasonInput')

const reason = ref<string>('')

const isOpen = ref<boolean>(false)

const form = useForm<Record<string, string>>({})

const onMarkAsUpdate = () => {
  form
    .transform(() => ({
      status: currentStatus.value?.next.tag,
    }))
    .patch(route('job_order.update', ticket), {
      preserveScroll: true,
      showProgress: false,
      onSuccess: (page: any) => {
        isOpen.value = false
        toast.success(page.props.flash.message, {
          position: 'top-right',
          // closeButton: true,
        })
      },
    })
}

const onMarkAsStop = () => {
  form
    .transform(() => ({
      status: currentStatus.value?.stop.tag,
      reason: reason.value,
    }))
    .post(route('job_order.cancel.create', ticket), {
      preserveScroll: true,
      showProgress: false,
      onError: () => reasonInput.value?.$el.focus(),
      onSuccess: () => (isOpen.value = false),
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
        <DialogTitle> Ticket Status Update </DialogTitle>
        <DialogDescription>
          {{ currentStatus!.description }}
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="mt-5">
        <div class="flex items-center gap-3">
          <Dialog>
            <DialogTrigger>
              <Button
                type="button"
                variant="destructive"
                :disabled="form.processing"
                class="font-normal"
              >
                {{ currentStatus?.stop.label }}
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle> Why? </DialogTitle>
                <DialogDescription>
                  Please provide a reason before permanently closing this
                  ticket.
                </DialogDescription>
              </DialogHeader>
              <Textarea
                ref="reasonInput"
                v-model="reason"
                id="reason"
                required
                placeholder=""
                :class="[
                  'h-[150px] w-full',
                  {
                    'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive':
                      form.errors.reason,
                  },
                ]"
              />
              <InputError
                class="-mt-3"
                :message="form.errors.reason"
              />
              <DialogFooter class="mt-5 flex items-center gap-3">
                <DialogClose>
                  <Button
                    variant="outline"
                    type="button"
                    :disabled="form.processing"
                  >
                    Go back
                  </Button>
                </DialogClose>
                <Button
                  variant="destructive"
                  type="button"
                  :disabled="form.processing"
                  @click="onMarkAsStop"
                >
                  <LoaderCircle
                    v-show="form.processing"
                    class="animate-spin"
                  />
                  {{ currentStatus?.stop.label }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
          <Button
            type="button"
            :disabled="form.processing"
            @click="onMarkAsUpdate"
          >
            <LoaderCircle
              v-show="form.processing"
              class="animate-spin"
            />
            {{ currentStatus?.next.label }}
          </Button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
