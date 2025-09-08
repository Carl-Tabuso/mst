<script setup lang="ts">
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useCorrections } from '@/composables/useCorrections'
import { JobOrder } from '@/types'

interface MachineDetailsProps {
  changes: any
  jobOrder: JobOrder
}

const { changes, jobOrder } = defineProps<MachineDetailsProps>()

const { getChangedOrCurrentValue } = useCorrections(changes, jobOrder)
</script>

<template>
  <div>
    <div class="mb-6">
      <div class="text-xl font-semibold leading-6">Machine Details</div>
      <p class="text-sm text-muted-foreground">
        Machine specifications and description of current problem.
      </p>
    </div>
    <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex flex-row items-center gap-x-4">
          <Label class="w-44 shrink-0"> Machine Type </Label>
          <Input
            v-bind="getChangedOrCurrentValue('machine_type')"
            class="pointer-events-none"
          />
        </div>
        <div class="flex flex-row items-center">
          <Label class="w-36 shrink-0"> Model </Label>
          <Input
            v-bind="getChangedOrCurrentValue('model')"
            class="pointer-events-none"
          />
        </div>
      </div>
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex flex-row items-center gap-x-4">
          <Label class="w-44 shrink-0"> Serial Number </Label>
          <Input
            v-bind="getChangedOrCurrentValue('serial_no')"
            class="pointer-events-none"
          />
        </div>
        <div class="flex flex-row items-center">
          <Label class="w-36 shrink-0"> Tag Number </Label>
          <Input
            v-bind="getChangedOrCurrentValue('tag_no')"
            class="pointer-events-none"
          />
        </div>
      </div>
      <div class="col-span-2">
        <div class="flex flex-row items-start gap-x-4">
          <Label class="mt-3 w-44 shrink-0"> Machine Problem </Label>
          <Textarea
            v-bind="getChangedOrCurrentValue('machine_problem')"
            class="pointer-events-none"
          />
        </div>
      </div>
    </div>
  </div>
</template>
