<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatToDateString } from '@/composables/useDateFormatter'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { parseDate } from '@internationalized/date'
import { Calendar } from 'lucide-vue-next'
import FormAreaInfo from '../FormAreaInfo.vue'
import SectionButton from '../SectionButton.vue'
import InputError from '@/components/InputError.vue'

interface FourthSectionProps {
  canEdit?: boolean
  status: JobOrderStatus
  errors: any
  isSubmitBtnDisabled?: boolean
}

interface FourthSectionEmits {
  onCancelSubmit: void
}

withDefaults(defineProps<FourthSectionProps>(), {
  canEdit: false,
  isSubmitBtnDisabled: false,
})

defineEmits<FourthSectionEmits>()

const startingDate = defineModel<any>('startingDate', {
  get(value) {
    if (value) {
      return parseDate(value.split('T')[0])
    }
  },
  default: '',
})
const endingDate = defineModel<any>('endingDate', {
  get(value) {
    if (value) {
      return parseDate(value.split('T')[0])
    }
  },
  default: '',
})

const { isPreHauling } = useWasteManagementStages()
</script>

<template>
  <FormAreaInfo
    :condition="isPreHauling(status)"
    class="mb-4"
  >
    <span class="pr-1 font-semibold">Dispatcher</span>
    is required to complete this section to continue with personnel assignment.
  </FormAreaInfo>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div>
      <div class="text-xl font-semibold leading-6">Hauling Duration</div>
      <p class="text-sm text-muted-foreground">
        Estimated starting and ending date of hauling operations.
      </p>
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-10">
      <div class="flex items-start gap-x-4">
        <Label class="w-44 shrink-0 mt-3">Start Date</Label>
        <div class="flex flex-col gap-1 w-full">
          <Popover>
            <PopoverTrigger
              as-child
              :disabled="!canEdit"
            >
              <Button
                type="button"
                variant="outline"
                :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !startingDate,
                    'border-destructive': errors.from
                  },
                ]"
              >
                <span>
                  {{
                    startingDate
                      ? formatToDateString(startingDate.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :model-value="startingDate"
                @update:model-value="
                  (value: any) => {
                    startingDate = new Date(value).toISOString()
                  }
                "
              />
            </PopoverContent>
          </Popover>
          <InputError :message="errors.from" />
        </div>
      </div>
      <div class="flex items-start">
        <Label class="w-36 shrink-0 mt-3">Finish Date</Label>
        <div class="flex flex-col gap-1 w-full">
          <Popover>
            <PopoverTrigger
              as-child
              :disabled="!canEdit"
            >
              <Button
                type="button"
                variant="outline"
                :class="[
                  'w-full ps-3 text-start font-normal',
                  { 'text-muted-foreground': !endingDate,
                    'border-destructive': errors.to
                  },
                ]"
              >
                <span>
                  {{
                    endingDate
                      ? formatToDateString(endingDate.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :model-value="endingDate"
                @update:model-value="
                  (value: any) => {
                    endingDate = new Date(value).toISOString()
                  }
                "
              />
            </PopoverContent>
          </Popover>
          <InputError :message="errors.to" />
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="canEdit"
    class="mt-6 flex justify-end space-x-2"
  >
    <SectionButton
      :is-submit-btn-disabled="isSubmitBtnDisabled"
      @on-cancel-submit="$emit('onCancelSubmit')"
    />
  </div>
</template>
