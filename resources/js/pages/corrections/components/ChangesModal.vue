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
import {
  CorrectionFieldKey,
  useCorrections,
} from '@/composables/useCorrections'
import { formatToDateDisplay } from '@/composables/useDateFormatter'
import { usePermissions } from '@/composables/usePermissions'
import { CorrectionStatusType } from '@/constants/correction-statuses'
import { CircleArrowRight, FileClock } from 'lucide-vue-next'
import { ref } from 'vue'
import ConfirmStatus from './ConfirmStatus.vue'

interface ChangesModalProps {
  changes: any
  status: CorrectionStatusType
}

const props = defineProps<ChangesModalProps>()

const isDialogOpen = ref<boolean>(false)

const { before, after } = props.changes

const { fieldMap, isDateString } = useCorrections()

const mappedChanges = (Object.keys(before) as CorrectionFieldKey[]).map(
  (b) => ({
    field: fieldMap[b].label,
    oldValue: isDateString(b)
      ? formatToDateDisplay(before[b], 'MMMM d, yyyy')
      : before[b],
    newValue: isDateString(b)
      ? formatToDateDisplay(after[b], 'MMMM d, yyyy')
      : after[b],
  }),
)

const { can } = usePermissions()

const isApprovable =
  props.status === 'pending' && can('update:job_order_correction')
</script>

<template>
  <Dialog v-model="isDialogOpen">
    <DialogTrigger>
      <Button
        variant="link"
        class="text-yellow-500 dark:text-warning"
      >
        <FileClock class="stroke-2" />
        See Changes
      </Button>
    </DialogTrigger>
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle class="text-yellow-500 dark:text-warning">
          Viewing Changes
        </DialogTitle>
        <DialogDescription>
          Here are the previous changes with the new corrections. Review the
          changes below and decide whether to accept or reject the corrections.
        </DialogDescription>
      </DialogHeader>
      <div class="my-2 flex max-h-[60dvh] flex-col overflow-y-auto">
        <div
          v-for="{ field, oldValue, newValue } in mappedChanges"
          :key="field"
          class="py-1"
        >
          <div class="mb-1 text-xs font-medium text-muted-foreground">
            {{ field }}
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div
              class="break-words rounded-md bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/30 dark:text-red-300"
            >
              {{ oldValue ?? 'None provided' }}
            </div>
            <div
              class="break-words rounded-md bg-green-50 px-3 py-1 text-green-700 dark:bg-green-950/30 dark:text-green-300"
            >
              {{ newValue }}
            </div>
          </div>
        </div>
      </div>
      <DialogFooter>
        <DialogClose>
          <Button variant="outline"> Close </Button>
        </DialogClose>
        <Dialog>
          <DialogTrigger :disabled="!isApprovable">
            <Button
              variant="warning"
              :class="{ 'cursor-not-allowed opacity-50': !isApprovable }"
            >
              <CircleArrowRight class="mr-1 stroke-2" />
              Proceed to Approval
            </Button>
          </DialogTrigger>
          <ConfirmStatus
            :new-values="changes.after"
            @on-status-update="isDialogOpen = false"
          />
        </Dialog>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
