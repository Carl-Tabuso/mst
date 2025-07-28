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
import { usePermissions } from '@/composables/usePermissions'
import { useWasteManagementStages } from '@/composables/useWasteManagementStages'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { Employee } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { parseDate } from '@internationalized/date'
import { Calendar, ChevronsUpDown, UserRound, X } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import EmployeePopoverSelection from '../EmployeePopoverSelection.vue'
import FormAreaInfo from '../FormAreaInfo.vue'
import EmployeeCommandListPlaceholder from '../placeholders/EmployeeCommandListPlaceholder.vue'
import SectionButton from '../SectionButton.vue'

interface SecondSectionProps {
  status: JobOrderStatus
  employees?: Employee[]
  dispatcher: Employee | null
  appraisers: Employee[]
  appraisedDate: any
  serviceableId: number
}

const props = defineProps<SecondSectionProps>()

const appraisers = ref<Employee[]>(props.appraisers)
const appraisedDate = ref<any>(props.appraisedDate)

const form = useForm({
  status: props.status,
  appraisers: appraisers.value,
  appraised_date: appraisedDate.value,
})

const appraisedDateModel = computed(() => {
  return appraisedDate.value
    ? parseDate(appraisedDate.value.split('T')[0])
    : undefined
})

watch([appraisers, appraisedDate], ([newAppraisers, newAppraisedDate]) => {
  form.appraisers = newAppraisers
  form.appraised_date = newAppraisedDate
})

const { can } = usePermissions()
const { isForAppraisal } = useWasteManagementStages()

const forAppraisal = computed(() => isForAppraisal(props.status))
const canEdit = computed(() => can('assign:appraisers') && forAppraisal.value)

const removeAppraiser = (employee: Employee) => {
  const index = appraisers.value.findIndex((a) => a.id === employee.id)
  appraisers.value.splice(index, 1)
}

const handleEmployeeMultiselect = (employee: Employee) => {
  if (!canEdit.value) return

  const isExistingAppraiser = appraisers.value
    ?.map((a) => a.id)
    .includes(employee.id)

  isExistingAppraiser
    ? removeAppraiser(employee)
    : appraisers.value.push(employee)
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

const onSubmit = () => {
  form.patch(route('job_order.waste_management.update', props.serviceableId), {
    preserveScroll: true,
    onSuccess: (page: any) => {
      toast.success(page.props.flash.message, {
        position: 'top-right',
      })
    },
  })
}
</script>

<template>
  <FormAreaInfo
    :condition="forAppraisal"
    class="mb-4"
  >
    <span class="px-1 font-semibold">Dispatcher </span>
    is required to complete this section during the
    <span class="px-1 font-semibold">For Appraisal</span>
    step to continue to site viewing.
  </FormAreaInfo>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div class="col-span-2 flex w-full items-start justify-between">
      <div>
        <div class="text-xl font-semibold leading-6">Ocular Inspection</div>
        <p class="text-sm text-muted-foreground">
          The assigned appraisers and appraisal date of site viewing.
        </p>
      </div>
      <div
        v-show="dispatcher"
        class="flex items-center font-medium text-muted-foreground"
      >
        <UserRound :size="16" class="mr-2" />
        <div class="text-xs">
          {{ `Completed by ${dispatcher?.fullName}` }}
        </div>        
      </div>
    </div>
    <div class="col-span-2 grid grid-cols-2 gap-x-24 gap-y-3">
      <div class="col-span-2 grid grid-cols-2 gap-x-10">
        <div class="flex items-start gap-x-4">
          <Label
            for="appraisers"
            class="mt-3 w-44 shrink-0"
          >
            Appraisers
          </Label>
          <div class="flex w-full flex-col gap-1">
            <Popover @update:open="(value) => handleAppraisersPopover(value)">
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="[{ 'border-destructive': form.errors.appraisers }]"
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
                        class="ml-1 h-5 w-5 text-muted-foreground hover:text-primary-foreground"
                        @click="() => (appraisers = [])"
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
              <PopoverContent class="w-72 p-0" align="start">
                <Command>
                  <CommandInput placeholder="Search for appraisers" />
                  <CommandList>
                    <CommandEmpty> No employee found. </CommandEmpty>
                    <template v-if="appraisers?.length">
                      <div
                        :class="['overflow-y-auto', { 'max-h-40': canEdit }]"
                      >
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
                          class="cursor-pointer"
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
            <InputError :message="form.errors.appraisers" />
          </div>
        </div>
        <div class="flex items-start">
          <Label class="mt-3 w-36 shrink-0"> Date Appraised </Label>
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
                      'text-muted-foreground': !appraisedDate,
                      'border-destructive': form.errors.appraised_date,
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
              <PopoverContent class="w-auto p-0" align="start">
                <AppCalendar
                  :model-value="appraisedDateModel"
                  @update:model-value="handleAppraisedDateChange"
                />
              </PopoverContent>
            </Popover>
            <InputError :message="form.errors.appraised_date" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="canEdit"
    class="mt-6 flex justify-end"
  >
    <SectionButton
      :is-submit-btn-disabled="form.processing"
      @on-submit="onSubmit"
      @on-cancel-submit="form.cancel()"
    />
  </div>
</template>
