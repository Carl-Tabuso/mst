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
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { ScrollArea } from '@/components/ui/scroll-area'
import { formatToDateString } from '@/composables/useDateFormatter'
import { JobOrderStatuses } from '@/constants/job-order-statuses'
import { Employee } from '@/types'
import { parseDate } from '@internationalized/date'
import { Calendar, ChevronsUpDown } from 'lucide-vue-next'
import { computed, ref } from 'vue'

interface ThirdSectionProps {
  employees: Employee[]
}

defineProps<ThirdSectionProps>()

const paymentType = defineModel<string>('paymentType')
const bidBond = defineModel<string | number>('bidBond')
const orNumber = defineModel<string>('orNumber')
const paymentDate = defineModel<any>('paymentDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})
const approvedDate = defineModel<any>('approvedDate', {
  get(value) {
    if (value) return parseDate(value.split('T')[0])
  },
  default: '',
})
const status = defineModel<string>('status')

const jobOrderStatus = computed(() =>
  JobOrderStatuses.find((s) => s.id === status.value),
)

const handlePaymentDateChange = (value: any) => {
  paymentDate.value = new Date(value).toISOString()
}

const handleApprovedDateChange = (value: any) => {
  approvedDate.value = new Date(value).toISOString()
}

const handleStatusChange = (value: string) => {
  status.value = value
  isStatusPopoverOpen.value = false
}

const isStatusPopoverOpen = ref<boolean>(false)
</script>

<template>
  <div class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          for="paymentType"
          class="w-48 shrink-0"
        >
          Type of Payment
        </Label>
        <Input
          id="paymentType"
          placeholder="Enter client's payment type"
          v-model="paymentType"
          class="w-full"
        />
      </div>
      <div class="flex items-center">
        <Label
          for="bidBond"
          class="w-36 shrink-0"
        >
          Bid Bond
        </Label>
        <Input
          id="bidBond"
          placeholder="Enter job order's bid bond"
          v-model="bidBond"
          class="w-full"
        />
      </div>
    </div>

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          for="orNumber"
          class="w-48 shrink-0"
        >
          OR Number
        </Label>
        <Input
          id="orNumber"
          placeholder="Enter OR Number"
          v-model="orNumber"
          class="w-full"
        />
      </div>
      <div class="flex items-center">
        <Label
          for="paymentDate"
          class="w-36 shrink-0"
        >
          Date of Payment
        </Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              type="button"
              variant="outline"
              :class="[
                'w-full ps-3 text-start font-normal',
                { 'text-muted-foreground': !paymentDate },
              ]"
            >
              <span>
                {{
                  paymentDate
                    ? formatToDateString(paymentDate.toString())
                    : 'Pick a date'
                }}
              </span>
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <AppCalendar
              :model-value="paymentDate"
              @update:model-value="handlePaymentDateChange"
            />
          </PopoverContent>
        </Popover>
      </div>
    </div>

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
      <div class="flex items-center gap-x-4">
        <Label
          for="approvedDate"
          class="w-48 shrink-0"
        >
          Date Approved
        </Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button
              type="button"
              variant="outline"
              :class="[
                'w-full ps-3 text-start font-normal',
                { 'text-muted-foreground': !approvedDate },
              ]"
            >
              <span>
                {{
                  approvedDate
                    ? formatToDateString(approvedDate.toString())
                    : 'Pick a date'
                }}
              </span>
              <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <AppCalendar
              :max-value="approvedDate"
              :model-value="approvedDate"
              @update:model-value="handleApprovedDateChange"
            />
          </PopoverContent>
        </Popover>
      </div>
      <div class="flex items-center">
        <Label
          for="status"
          class="w-36 shrink-0"
        >
          Status
        </Label>
        <Popover v-model:open="isStatusPopoverOpen">
          <PopoverTrigger
            class="w-[400px]"
            as-child
          >
            <Button
              variant="outline"
              class=""
            >
              <Badge
                class="overflow-hidden truncate text-ellipsis rounded-full font-normal"
                :variant="jobOrderStatus?.badge"
                >{{ jobOrderStatus?.label }}
              </Badge>
              <ChevronsUpDown class="ml-auto h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent class="p-0">
            <Command>
              <CommandInput placeholder="Search for a badge" />
              <CommandList>
                <CommandEmpty> No results found. </CommandEmpty>
                <ScrollArea class="h-72">
                  <CommandGroup>
                    <div v-for="joStatus in JobOrderStatuses">
                      <CommandItem
                        v-if="joStatus.id !== jobOrderStatus?.id"
                        :key="joStatus.id"
                        :value="joStatus"
                        @select="handleStatusChange(joStatus.id)"
                      >
                        <Badge :variant="joStatus.badge">
                          {{ joStatus.label }}
                        </Badge>
                      </CommandItem>
                    </div>
                  </CommandGroup>
                </ScrollArea>
              </CommandList>
            </Command>
          </PopoverContent>
        </Popover>
      </div>
    </div>
  </div>
</template>
