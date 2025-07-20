<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import InputError from '@/components/InputError.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandSeparator,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { Calendar, ChevronsUpDown, X } from 'lucide-vue-next'
import { computed } from 'vue'
import { toast } from 'vue-sonner'
import EmployeePopoverSelection from '../EmployeePopoverSelection.vue'
import FormAreaInfo from '../FormAreaInfo.vue'
import EmployeeCommandListPlaceholder from '../placeholders/EmployeeCommandListPlaceholder.vue'
import SectionButton from '../SectionButton.vue'

interface SecondSectionProps {
  canEdit?: boolean
  status: JobOrderStatus
  errors: any
  employees?: Employee[]
  isSubmitBtnDisabled: boolean
}

const props = withDefaults(defineProps<SecondSectionProps>(), {
  canEdit: false,
  isSubmitBtnDisabled: false,
})

const appraisers = defineModel<Employee[]>('appraisers', {
  default: () => [],
})
const appraisedDate = defineModel<any>('appraisedDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})

const removeAppraiser = (employee: Employee) => {
  const index = appraisers.value.findIndex((a) => a.id === employee.id)
  appraisers.value.splice(index, 1)
}

const handleEmployeeMultiselect = (employee: Employee) => {
  if (!props.canEdit) return

  const isExistingAppraiser = appraisers.value
    ?.map((a) => a.id)
    .includes(employee.id)

  if (isExistingAppraiser) {
    removeAppraiser(employee)
    toast.info(`Removed ${employee.fullName} as appraiser`, {
      position: 'top-right',
      action: {
        label: 'Undo',
        onClick: () => appraisers.value.push(employee),
      },
    })
  } else {
    appraisers.value.push(employee)
    toast.success(`Added ${employee.fullName} as appraiser`, {
      position: 'top-right',
    })
  }
}

const removeAllAppraisers = () => {
  const temp = appraisers.value

  appraisers.value = []

  toast.warning(`Removed all ${temp.length} appraisers`, {
    position: 'top-right',
    action: {
      label: 'Undo',
      onClick: () => (appraisers.value = temp),
    },
  })
}

const handleAppraisedDateChange = (value: any) => {
  appraisedDate.value = new Date(value).toISOString()
}

const emit = defineEmits<{
  (e: 'loadEmployees'): void
  (e: 'onCancelSubmit'): void
}>()

const handleAppraisersPopover = (isOpen: boolean) => {
  if (isOpen && props.employees === undefined) {
    emit('loadEmployees')
  }
}

const remainingEmployees = computed(() => {
  const filtered = props.employees?.filter(
    (e) => !appraisers.value?.flatMap((a) => a.id).includes(e.id),
  )

  return new Set(filtered)
})

const { isForAppraisal } = useWasteManagementStages()
</script>

<template>
  <FormAreaInfo
    :condition="isForAppraisal(status)"
    class="mb-3"
  >
    <span class="px-1 font-semibold">Dispatcher </span>
    is required to complete this section during the
    <span class="px-1 font-semibold">For Appraisal</span>
    step to continue to site viewing.
  </FormAreaInfo>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div class="flex justify-between">
      <div>
        <div class="text-xl font-semibold leading-6">Ocular Inspection</div>
        <p class="text-sm text-muted-foreground">
          The assigned appraisers and appraisal date of site viewing.
        </p>
      </div>
      <div class="">
        <!-- timestamp -->
      </div>
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-10 gap-y-3">
      <div class="space-y-2">
        <div class="flex items-center gap-x-4">
          <Label
            for="appraisers"
            class="w-44 shrink-0"
          >
            Appraisers
          </Label>
          <Popover @update:open="(value) => handleAppraisersPopover(value)">
            <PopoverTrigger
              class="w-[400px]"
              as-child
            >
              <Button
                variant="outline"
                :class="[
                  'bg-muted',
                  { 'border-destructive': errors.appraisers },
                ]"
              >
                <template v-if="appraisers?.length">
                  <div
                    :key="appraisers[0].id"
                    class="flex items-center justify-between gap-2 rounded-md text-xs"
                  >
                    <div class="flex items-center gap-2 overflow-hidden">
                      <Avatar class="h-7 w-7 shrink-0 rounded-full">
                        <AvatarImage
                          v-if="appraisers[0]?.account?.avatar"
                          :src="appraisers[0].account.avatar"
                          :alt="appraisers[0].fullName"
                        />
                        <AvatarFallback>
                          {{ getInitials(appraisers[0].fullName) }}
                        </AvatarFallback>
                      </Avatar>
                      <span class="truncate">
                        <template v-if="appraisers.length < 2">
                          {{ appraisers[0].fullName }}
                        </template>
                        <template v-else>
                          {{
                            `${appraisers[0].fullName} and ${appraisers.length - 1} more`
                          }}
                        </template>
                      </span>
                    </div>
                    <Button
                      v-if="canEdit"
                      variant="ghost"
                      size="icon"
                      type="button"
                      class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                      @click="
                        () =>
                          appraisers?.length < 2
                            ? handleEmployeeMultiselect(appraisers[0])
                            : removeAllAppraisers()
                      "
                    >
                      <X />
                    </Button>
                  </div>
                </template>
                <template v-else>
                  <span class="font-normal text-muted-foreground">
                    Select appraisers
                  </span>
                </template>
                <ChevronsUpDown class="ml-auto h-4 w-4" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-72 p-0">
              <Command>
                <CommandInput placeholder="Search for appraisers" />
                <CommandList>
                  <CommandEmpty> No employee found. </CommandEmpty>
                  <template v-if="appraisers?.length">
                    <div :class="['overflow-y-auto', { 'max-h-40': canEdit }]">
                      <CommandGroup>
                        <CommandItem
                          v-for="appraiser in appraisers"
                          :key="appraiser.id"
                          :value="appraiser"
                          @select="() => handleEmployeeMultiselect(appraiser)"
                        >
                          <EmployeePopoverSelection
                            :employee="appraiser"
                            is-selected
                            is-disabled
                          />
                        </CommandItem>
                      </CommandGroup>
                    </div>
                    <CommandSeparator v-if="canEdit" />
                  </template>
                  <CommandGroup v-if="canEdit">
                    <template v-if="!employees">
                      <EmployeeCommandListPlaceholder />
                    </template>
                    <template v-else>
                      <CommandItem
                        v-for="employee in remainingEmployees"
                        :key="employee.id"
                        :value="employee"
                        @select="() => handleEmployeeMultiselect(employee)"
                      >
                        <EmployeePopoverSelection :employee="employee" />
                      </CommandItem>
                    </template>
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
        </div>
        <div
          v-if="errors.appraisers"
          class="flex items-center gap-x-4"
        >
          <div class="w-44"></div>
          <InputError :message="errors.appraisers" />
        </div>
      </div>
      <div class="space-y-2">
        <div class="flex items-center">
          <Label class="w-36 shrink-0"> Date Appraised </Label>
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
                    'text-muted-foreground': !appraisedDate,
                    'border-destructive': errors.appraised_date,
                  },
                ]"
              >
                <span>
                  {{
                    appraisedDate
                      ? formatToDateString(appraisedDate.toString())
                      : 'Pick a date'
                  }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
              <AppCalendar
                :model-value="appraisedDate"
                @update:model-value="handleAppraisedDateChange"
              />
            </PopoverContent>
          </Popover>
        </div>
        <div
          v-if="errors.appraised_date"
          class="flex items-center"
        >
          <div class="w-36"></div>
          <InputError :message="errors.appraised_date" />
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
