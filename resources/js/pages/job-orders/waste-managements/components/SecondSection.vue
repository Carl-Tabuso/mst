<script setup lang="ts">
import { Label } from '@/components/ui/label'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Calendar, ChevronsUpDown, CheckIcon } from 'lucide-vue-next'
import AppCalendar from '@/components/AppCalendar.vue'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { 
  Command, 
  CommandEmpty, 
  CommandGroup, 
  CommandInput, 
  CommandItem, 
  CommandList 
} from '@/components/ui/command'
import { Badge } from '@/components/ui/badge'
import { Employee } from '@/types'
import { formatToDateString } from '@/composables/useDateFormatter'
import { ref } from 'vue'
import { parseDate } from '@internationalized/date'

defineProps<{ employees: Employee[] }>()

const appraisers = defineModel<Employee[]>('appraisers')
const appraisedDate = defineModel<any>('appraisedDate', {
    get(value) { return parseDate(value.split('T')[0]) }
})

const isExistingAppraiser = (employeeId: number) => {
  return appraisers.value?.map((a) => a.id).includes(employeeId)
}

const handleEmployeeMultiselect = (employee: Employee) => {
    if(appraisers.value?.length) {
        if (isExistingAppraiser(employee.id)) {
            const index = appraisers.value.findIndex((a) => a.id === employee.id)
            
            appraisers.value.splice(index, 1)
        }        
    } else {
    if (appraisers.value === undefined) {
      appraisers.value = new Array()
    }
    appraisers.value.push(employee)
  }
}

const handleAppraisedDateChange = (value: any) => {
  appraisedDate.value = new Date(value).toISOString()
  isAppraisedDatePopoverOpen.value = false
}

const isAppraisedDatePopoverOpen = ref<boolean>(false)
</script>

<template>
    <div class="col-span-2 grid grid-cols-2 gap-x-24">
        <div class="flex items-center gap-x-4">
            <Label for="appraisers" class="w-48 shrink-0">
            Appraisers
            </Label>
            <Popover>
            <PopoverTrigger class="w-[400px]" as-child>
                <Button variant="outline" class="">
                <template v-if="appraisers?.length">
                    <Badge
                    v-if="appraisers?.length <= 2"
                    v-for="appraiser in appraisers?.slice(0, 2)"
                    :key="appraiser.id"
                    class="rounded-full font-normal truncate overflow-hidden text-ellipsis"
                    variant="secondary"
                    >{{ appraiser.fullName }}
                    </Badge>
                    <template v-else>
                    <Badge
                        v-for="appraiser in appraisers?.slice(0, 1)"
                        :key="appraiser.id"
                        class="rounded-full font-normal"
                        variant="secondary"
                    >{{ `${appraiser.fullName} and ${appraisers.length - 1} more` }}
                    </Badge>
                    </template>
                </template>
                <template v-else>
                    <span class="text-muted-foreground">
                    Select appraisers
                    </span>
                </template>
                <ChevronsUpDown class="ml-auto h-4 w-4" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="p-0">
                <Command>
                <CommandInput
                    placeholder="Search for appraisers" />
                <CommandList>
                    <CommandEmpty>
                    No employee found.
                    </CommandEmpty>
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
            <Label class="w-36 shrink-0">
            Date Appraised
            </Label>
            <Popover v-model:open="isAppraisedDatePopoverOpen">
            <PopoverTrigger as-child>
                <Button
                type="button"
                variant="outline"
                class="w-full ps-3 text-start font-normal"
                >
                <span>
                    {{ formatToDateString(appraisedDate.toString()) }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
                <AppCalendar
                    :max-value="appraisedDate"
                    :model-value="appraisedDate"
                    @update:model-value="handleAppraisedDateChange"
                />
            </PopoverContent>
            </Popover>                  
        </div>
    </div>
</template>
