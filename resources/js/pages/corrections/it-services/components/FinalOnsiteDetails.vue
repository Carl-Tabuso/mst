<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useCorrections } from '@/composables/useCorrections';
import { JobOrder } from '@/types';

interface FinalOnsiteDetailsProps {
  changes: any
  jobOrder: JobOrder
}

const { changes, jobOrder } = defineProps<FinalOnsiteDetailsProps>()

const { getChangedOrCurrentValue } = useCorrections(changes, jobOrder)
</script>

<template>
  <div>
    <div class="mb-6">
      <div>
        <div class="text-xl font-semibold leading-6 text-foreground">
          Final Onsite Visit
        </div>
        <p class="text-sm text-muted-foreground">
          The final onsite report details.
        </p>
      </div>
    </div>
    <div class="mt-4 grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
      <div class="col-span-2 flex flex-row items-start">
        <Label class="mt-3 w-44 shrink-0">
          Services Performed
        </Label>
        <Textarea
          v-bind="getChangedOrCurrentValue('final_service_performed')"
          class="pointer-events-none"
        />
      </div>
      <div class="col-span-2 flex flex-row items-start">
        <Label class="mt-3 w-44 shrink-0">
          Parts Replaced
        </Label>
        <Textarea
          v-bind="getChangedOrCurrentValue('parts_replaced')"
          class="pointer-events-none"
        />
      </div>
      <div class="col-span-2 flex flex-row items-start">
        <Label class="mt-3 w-44 shrink-0">
          Remarks
        </Label>
        <Textarea
          v-bind="getChangedOrCurrentValue('remarks')"
          class="pointer-events-none"
        />
      </div>
      <div class="col-span-2 w-[50%] flex flex-row items-center">
        <Label class="w-44 shrink-0">
          Machine Status
        </Label>
        <Input
          v-bind="getChangedOrCurrentValue('final_machine_status')"
          class="pointer-events-none"
        />
      </div>
    </div>
  </div>
</template>
