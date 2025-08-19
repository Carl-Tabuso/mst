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
import { CircleArrowRight, FileClock } from 'lucide-vue-next'
import ConfirmStatus from './ConfirmStatus.vue'

interface ChangesModalProps {
  changes: any
}

interface Change {
  field: string
  oldValue: string
  newValue: string
}

const mockChanges: Change[] = [
  {
    field: 'Employee Name',
    oldValue: 'Juan Dela Cruz',
    newValue: 'Juan De la Cruz',
  },
  {
    field: 'Job Title',
    oldValue: 'Software Engr.',
    newValue: 'Software Engineer',
  },
  { field: 'Date Hired', oldValue: '2023-01-15', newValue: '2023-01-20' },
  { field: 'Department', oldValue: 'IT', newValue: 'Information Technology' },
]
</script>

<template>
  <Dialog>
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
          Comparing Changes
        </DialogTitle>
        <DialogDescription>
          Here are the previous changes with the new corrections. Review the
          changes below and decide whether to accept or reject the corrections.
        </DialogDescription>
      </DialogHeader>
      <div
        class="flex max-h-[90dvh] flex-col overflow-y-auto rounded-md border px-4 py-2"
      >
        <div
          v-for="change in mockChanges"
          :key="change.field"
          class="py-1"
        >
          <div class="mb-1 text-xs font-medium text-muted-foreground">
            {{ change.field }}
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div
              class="rounded-md bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/30 dark:text-red-300"
            >
              {{ change.oldValue }}
            </div>
            <div
              class="rounded-md bg-green-50 px-3 py-1 text-green-700 dark:bg-green-950/30 dark:text-green-300"
            >
              {{ change.newValue }}
            </div>
          </div>
        </div>
      </div>
      <DialogFooter>
        <DialogClose>
          <Button variant="outline"> Close </Button>
        </DialogClose>
        <Dialog>
          <DialogTrigger>
            <Button variant="warning">
              <CircleArrowRight class="mr-1 stroke-2" />
              Proceed to Approval
            </Button>
          </DialogTrigger>
          <ConfirmStatus />
        </Dialog>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
