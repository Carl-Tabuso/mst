<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
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
import { ScrollArea } from '@/components/ui/scroll-area'
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { Calendar, CheckIcon, ChevronsUpDown, X } from 'lucide-vue-next'

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

const removeExistingAppraisers = () => {
  appraisers.value = []
}

const handleAppraisedDateChange = (value: any) => {
  appraisedDate.value = new Date(value).toISOString()
}
</script>

<template>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
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
              class="bg-muted"
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
                    variant="ghost"
                    size="icon"
                    type="button"
                    class="ml-1 h-5 w-5 text-muted-foreground hover:text-foreground"
                    @click="
                      () =>
                        appraisers?.length < 2
                          ? handleEmployeeMultiselect(appraisers[0])
                          : removeExistingAppraisers()
                    "
                  >
                    <X />
                  </Button>
                </div>
              </template>

              <template v-else>
                <span class="text-muted-foreground"> Select appraisers </span>
              </template>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-full p-0">
            <Command>
              <CommandInput placeholder="Search for appraisers" />
              <CommandList>
                <CommandEmpty> No employee found. </CommandEmpty>
                <ScrollArea class="h-72">
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
                        <CheckIcon class="h-4 w-4" />
                      </div>
                      <Avatar class="h-7 w-7 overflow-hidden rounded-full">
                        <AvatarImage
                          v-if="employee?.account?.avatar"
                          :src="employee?.account?.avatar"
                          :alt="employee.fullName"
                        />
                        <AvatarFallback class="rounded-full">
                          {{ getInitials(employee.fullName) }}
                        </AvatarFallback>
                      </Avatar>
                      <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate">
                          {{ employee.fullName }}
                        </span>
                      </div>
                    </CommandItem>
                  </CommandGroup>
                </ScrollArea>
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
  </div>
</template>
