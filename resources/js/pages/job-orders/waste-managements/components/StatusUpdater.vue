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
import { CircleArrowRight, LoaderCircle } from 'lucide-vue-next'
import { computed, ref, useTemplateRef } from 'vue'

interface StatusUpdaterProps {
  status: JobOrderStatus
  form: any
}

interface StatusUpdaterEmits {
  markAsUpdate: [next: JobOrderStatus]
  markAsStop: [stop: JobOrderStatus, reason: string]
}

const props = defineProps<StatusUpdaterProps>()

defineEmits<StatusUpdaterEmits>()

const currentStatus = computed(() =>
  manualUpdateStatuses.find((msu) => msu.id === props.status),
)

const reasonInput = useTemplateRef('reasonInput')

const reason = ref<string>('')

const isOpen = ref<boolean>(false)

defineExpose({ reasonInput, isOpen })
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
        <DialogTitle> Read Carefully! </DialogTitle>
      </DialogHeader>
      <div class="text-sm text-muted-foreground">
        {{ currentStatus?.description }}
      </div>
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
                  @click="$emit('markAsStop', currentStatus!.stop.tag, reason)"
                >
                  {{ currentStatus?.stop.label }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
          <Button
            type="button"
            :disabled="form.processing"
            @click="$emit('markAsUpdate', currentStatus!.next.tag)"
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
