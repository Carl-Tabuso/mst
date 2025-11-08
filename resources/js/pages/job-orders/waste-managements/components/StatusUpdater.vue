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
import { CircleArrowRight, CircleMinus, LoaderCircle } from 'lucide-vue-next'
import { computed, ref, useTemplateRef } from 'vue'
import { toast } from 'vue-sonner'

interface StatusUpdaterProps {
  status: JobOrderStatus
  ticket: string
}

const props = defineProps<StatusUpdaterProps>()

const currentStatus = computed(() => {
  return manualUpdateStatuses.find((msu) => msu.id === props.status)
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
    .patch(route('job_order.update', props.ticket), {
      preserveScroll: true,
      preserveState: false,
      showProgress: false,
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message, {
          position: 'top-right',
        })
      },
      onFinish: () => (isOpen.value = false),
    })
}

const onMarkAsStop = () => {
  form
    .transform(() => ({
      status: currentStatus.value?.stop.tag,
      reason: reason.value,
    }))
    .post(route('job_order.cancel.create', props.ticket), {
      preserveScroll: true,
      showProgress: false,
      onError: () => reasonInput.value?.$el.focus(),
      onSuccess: (page: any) => {
        toast.info(page.props.flash.message, {
          position: 'top-right',
        })
      },
      onFinish: () => (isOpen.value = false),
    })
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger>
      <Button type="button">
        <CircleArrowRight class="mr-2" />
        <span class="flex sm:hidden"> Update </span>
        <span class="hidden sm:flex"> Update Status </span>
      </Button>
    </DialogTrigger>
    <DialogContent class="max-w-[600px]">
      <DialogHeader>
        <DialogTitle class="font-bold">
          <span> Ticket: </span>
          <span class="text-muted-foreground"> {{ ticket }} </span>
        </DialogTitle>
        <DialogDescription>
          You can update the status of this ticket here.
        </DialogDescription>
      </DialogHeader>
      <div class="space-y-4">
        <div
          class="flex flex-row items-center justify-between gap-6 rounded-md border p-4 shadow"
        >
          <div class="flex-1">
            <p class="mt-1 text-sm text-muted-foreground">
              {{ currentStatus?.next.description }}
            </p>
          </div>
          <Button
            type="button"
            :disabled="form.processing"
            @click="onMarkAsUpdate"
            class="font-normal"
          >
            <LoaderCircle
              v-if="form.processing"
              class="animate-spin"
            />
            <CircleArrowRight
              v-else
              class="size-4"
            />
            {{ currentStatus?.next.label }}
          </Button>
        </div>
        <Dialog>
          <div
            class="flex flex-row items-center justify-between gap-6 rounded-md border p-4 shadow"
          >
            <div class="flex-1">
              <p class="mt-1 text-sm leading-5 text-muted-foreground">
                {{ currentStatus?.stop.description }}
              </p>
            </div>
            <DialogTrigger>
              <Button
                type="button"
                variant="destructive"
                :disabled="form.processing"
                class="font-normal"
              >
                <CircleMinus class="size-4" />
                {{ currentStatus?.stop.label }}
              </Button>
            </DialogTrigger>
          </div>
          <DialogContent>
            <DialogHeader>
              <DialogTitle> Why? </DialogTitle>
              <DialogDescription>
                Please provide a reason before permanently closing this ticket.
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
      </div>
      <DialogFooter class="mt-3">
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
