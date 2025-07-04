<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { formatToDateString } from '@/composables/useDateFormatter'
import { cn } from '@/lib/utils'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { Calendar, CheckIcon, ChevronsUpDown } from 'lucide-vue-next'

interface SecondSectionProps {
  employees: Employee[]
  isAppraisersInputDisabled?: boolean
  isAppraisedDateInputDisabled?: boolean
}

withDefaults(defineProps<SecondSectionProps>(), {
  isAppraisersInputDisabled: false,
  isAppraisedDateInputDisabled: false,
})

const appraisers = defineModel<Employee[]>('appraisers')
const appraisedDate = defineModel<any>('appraisedDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})

const isExistingAppraiser = (employeeId: number) => {
  return appraisers.value?.map((a) => a.id).includes(employeeId)
}

const handleEmployeeMultiselect = (employee: Employee) => {
  if (appraisers.value === undefined) {
    appraisers.value = []
  }

  if (isExistingAppraiser(employee.id)) {
    const index = appraisers.value.findIndex((a) => a.id === employee.id)
    appraisers.value.splice(index, 1)
  } else {
    appraisers.value.push(employee)
  }
}

const handleAppraisedDateChange = (value: any) => {
  appraisedDate.value = new Date(value).toISOString()
}
</script>

<template>
  <div class="col-span-2 grid grid-cols-2 gap-x-24">
    <div class="flex items-center gap-x-4">
      <Label
        for="appraisers"
        class="w-48 shrink-0"
      >
        Appraisers
      </Label>
      <Popover>
        <PopoverTrigger
          class="w-[400px]"
          as-child
          :disabled="isAppraisersInputDisabled"
        >
          <Button
            variant="outline"
            class=""
          >
            <template v-if="appraisers?.length">
              <template v-if="appraisers.length < 3">
                <Badge
                  v-for="appraiser in appraisers?.slice(0, 2)"
                  :key="appraiser.id"
                  class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                  variant="secondary"
                  >{{ appraiser.fullName }}
                </Badge>
              </template>
              <template v-else>
                <Badge
                  v-for="appraiser in appraisers?.slice(0, 1)"
                  :key="appraiser.id"
                  class="rounded-full font-normal"
                  variant="secondary"
                  >{{
                    `${appraiser.fullName} and ${appraisers.length - 1} more`
                  }}
                </Badge>
              </template>
            </template>
            <template v-else>
              <span class="text-muted-foreground"> Select appraisers </span>
            </template>
            <ChevronsUpDown class="ml-auto h-4 w-4" />
          </Button>
        </PopoverTrigger>
        <PopoverContent class="p-0">
          <Command>
            <CommandInput placeholder="Search for appraisers" />
            <CommandList>
              <CommandEmpty> No employee found. </CommandEmpty>
              <CommandGroup>
                <CommandItem
                  v-for="employee in employees"
                  :key="employee.id"
                  :value="employee"
                  @select="() => handleEmployeeMultiselect(employee)"
                >
                  <div
                    :class="[
                      'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                      isExistingAppraiser(employee.id)
                        ? 'bg-primary text-primary-foreground'
                        : 'opacity-50 [&_svg]:invisible',
                    ]"
                  >
                    <CheckIcon :class="cn('h-4 w-4')" />
                  </div>
                  <span>
                    {{ employee.fullName }}
                  </span>
                </CommandItem>
              </CommandGroup>
            </CommandList>
          </Command>
        </PopoverContent>
      </Popover>
    </div>
    <div class="flex items-center">
      <Label class="w-36 shrink-0"> Date Appraised </Label>
      <Popover>
        <PopoverTrigger
          as-child
          :disabled="isAppraisedDateInputDisabled"
        >
          <Button
            type="button"
            variant="outline"
            :class="[
              'w-full ps-3 text-start font-normal',
              { 'text-muted-foreground': !appraisedDate },
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
  </div>
</template>
