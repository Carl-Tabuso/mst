<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { useForm } from '@inertiajs/vue3'
import { CircleCheck, LoaderCircle } from 'lucide-vue-next'
import { inject, computed } from 'vue'
import { toast } from 'vue-sonner'

interface ConfirmStatusProps {
  newValues: any
}

const { newValues } = defineProps<ConfirmStatusProps>()

const selections = [
  {
    label: 'Approve',
    value: 'approved',
  },
  {
    label: 'Reject',
    value: 'rejected',
  },
] as const

// Prepare the data for the patch request
const prepareFormData = () => {
  const data: any = { ...newValues };
  
  // If we have serviceable data, we need to structure it properly
  if (newValues.serviceable) {
    // The backend expects serviceable data to be nested under 'serviceable' key
    data.serviceable = { ...newValues.serviceable };
  }
  
  return data;
}

const form = useForm({
  status: '',
  new_values: prepareFormData(),
})

const emit = defineEmits<{
  (e: 'onStatusUpdate'): void
}>()

const correctionId = inject<number>('correctionId')

const onSubmit = () => {
  form.patch(route('job_order.correction.update', correctionId), {
    onStart: () => form.clearErrors(),
    showProgress: false,
    preserveState: true,
    replace: true,
    onSuccess: (page: any) => {
      toast(page.props.flash.message, {
        position: 'top-center',
      })
    },
    onFinish: () => emit('onStatusUpdate'),
  })
}
</script>

<template>
  <DialogContent>
    <DialogHeader>
      <DialogTitle> Confirm Action on Job Order Correction </DialogTitle>
      <DialogDescription>
        You are reviewing a correction request for a Job Order. Choose whether
        to approve or reject the proposed changes:
      </DialogDescription>
    </DialogHeader>
    <form @submit.prevent="onSubmit">
      <div>
        <RadioGroup
          required
          v-model="form.status"
        >
          <div
            v-for="(status, index) in selections"
            :key="index"
            class="flex items-center gap-x-2 py-1"
          >
            <RadioGroupItem
              :id="status.value"
              :value="status.value"
            />
            <Label :for="status.value">
              {{ status.label }}
            </Label>
          </div>
        </RadioGroup>
        <InputError
          class="mt-3"
          :message="form.errors.status"
        />
      </div>
      <DialogFooter>
        <DialogClose>
          <Button
            variant="outline"
            type="button"
          >
            Cancel
          </Button>
        </DialogClose>
        <Button
          type="submit"
          :disabled="form.processing"
        >
          <LoaderCircle
            v-show="form.processing"
            class="animate-spin"
          />
          <CircleCheck class="stroke-2" />
          Set Status
        </Button>
      </DialogFooter>
    </form>
  </DialogContent>
</template>