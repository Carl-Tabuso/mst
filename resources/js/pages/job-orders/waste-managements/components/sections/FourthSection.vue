<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import InputError from '@/components/InputError.vue'
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
import { Employee } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { parseDate } from '@internationalized/date'
import { formatDistanceStrict, subDays } from 'date-fns'
import { Calendar, CalendarClock } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'
import FormAreaInfo from '../FormAreaInfo.vue'
import SectionButton from '../SectionButton.vue'

interface FourthSectionProps {
  status: JobOrderStatus
  startingDate: any
  endingDate: any
  serviceableId: number
  dispatcher: Employee | null
}

const props = defineProps<FourthSectionProps>()

const { canUpdateHaulingDuration } = useWasteManagementStages()
const canEdit = computed(() => canUpdateHaulingDuration(props.status))

const startingDate = ref<any>(props.startingDate)
const endingDate = ref<any>(props.endingDate)

const startingDateModel = computed(() => {
  return startingDate.value
    ? parseDate(startingDate.value.split('T')[0])
    : undefined
})

const endingDateModel = computed(() => {
  return endingDate.value
    ? parseDate(endingDate.value.split('T')[0])
    : undefined
})

const minDate = computed(() =>
  parseDate(new Date().toISOString().split('T')[0]),
)

const form = useForm<Record<string, string>>({
  status: props.status,
})

const onSubmit = () => {
  form
    .transform((data) => ({
      ...data,
      from: startingDate.value,
      to: endingDate.value,
    }))
    .patch(route('job_order.waste_management.update', props.serviceableId), {
      preserveScroll: true,
      onSuccess: (page: any) => {
        toast.success(page.props.flash.message, {
          position: 'top-right',
        })
      },
    })
}

const { isPreHauling } = useWasteManagementStages()
</script>

<template>
  <FormAreaInfo
    :condition="isPreHauling(status)"
    class="mb-4"
  >
    <span class="font-semibold">Dispatcher</span>
    is required to complete this section to continue with personnel assignment.
  </FormAreaInfo>
  <div class="grid grid-cols-1 gap-x-6 gap-y-6 md:grid-cols-[auto,1fr]">
    <div
      class="col-span-1 grid grid-cols-1 items-start gap-y-3 md:col-span-2 md:grid-cols-3 md:gap-y-0"
    >
      <div>
        <div class="text-xl font-semibold leading-6">Hauling Duration</div>
        <p class="text-sm text-muted-foreground">
          Estimated starting and ending date of hauling operations.
        </p>
      </div>
      <div
        v-if="startingDate && endingDate"
        class="flex justify-start md:justify-center"
      >
        <div
          class="flex items-center gap-2 text-sm font-semibold text-muted-foreground"
        >
          <CalendarClock class="size-4" />
          {{
            formatDistanceStrict(
              subDays(new Date(startingDate), 1),
              new Date(endingDate),
            )
          }}
          of total duration
        </div>
      </div>
    </div>
    <div
      class="col-span-1 grid grid-cols-1 gap-x-8 gap-y-4 md:col-span-2 md:grid-cols-2"
    >
      <div class="flex flex-col gap-2 md:flex-row md:items-start md:gap-x-4">
        <Label class="mt-1 shrink-0 md:mt-3 md:w-44">Start Date</Label>
        <div class="flex w-full flex-col gap-1">
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
                  {
                    'text-muted-foreground': !startingDate,
                    'border-destructive': form.errors.from,
                  },
                ]"
              >
                <span>
                  {{
                    startingDateModel
                      ? formatToDateString(startingDateModel.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :min-value="minDate"
                :model-value="startingDateModel"
                @update:model-value="
                  (value: any) => {
                    startingDate = new Date(value).toISOString()
                  }
                "
              />
            </PopoverContent>
          </Popover>
          <InputError :message="form.errors.from" />
        </div>
      </div>
      <div class="flex flex-col gap-2 md:flex-row md:items-start md:gap-x-4">
        <Label class="mt-1 shrink-0 md:mt-3 md:w-36">Finish Date</Label>
        <div class="flex w-full flex-col gap-1">
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
                  {
                    'text-muted-foreground': !endingDate,
                    'border-destructive': form.errors.to,
                  },
                ]"
              >
                <span>
                  {{
                    endingDateModel
                      ? formatToDateString(endingDateModel.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :min-value="minDate"
                :model-value="endingDateModel"
                @update:model-value="
                  (value: any) => {
                    endingDate = new Date(value).toISOString()
                  }
                "
              />
            </PopoverContent>
          </Popover>
          <InputError :message="form.errors.to" />
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="canEdit"
    class="mt-6 flex justify-end space-x-2"
  >
    <SectionButton
      :is-submit-btn-disabled="form.processing"
      @on-submit="onSubmit"
      @on-cancel-submit="form.cancel()"
    />
  </div>
</template>
