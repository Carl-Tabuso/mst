<script setup lang="ts">
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Label } from '@/components/ui/label'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Calendar } from 'lucide-vue-next'
import AppCalendar from '@/components/AppCalendar.vue'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Textarea } from '@/components/ui/textarea'
import { parseDate } from '@internationalized/date'
import { formatToDateString } from '@/composables/useDateFormatter'

const serviceType = defineModel<string>('serviceType')
const serviceDate = defineModel<any>('serviceDate', {
    get(value) { return parseDate(value.split('T')[0]) }
})
const serviceTime = defineModel<string>('serviceTime')
const client = defineModel<string>('client')
const address = defineModel<string>('address')
const department = defineModel<string>('department')
const contactPosition = defineModel<string>('contactPosition')
const contactNumber = defineModel<string>('contactNumber')
const contactPerson = defineModel<string>('contactPerson')

const isDateOfServicePopoverOpen = ref<boolean>(false)

const handleDateOfServiceChange = (value: any) => {
  serviceDate.value = new Date(value).toISOString()
  isDateOfServicePopoverOpen.value = false
}
</script>

<template>
    <!-- Type of Service -->
    <Label class="self-center">
        Type of Service
    </Label>
    <RadioGroup
        required
        v-model="serviceType" 
        class="flex items-center gap-x-10"
    >
        <div class="flex items-center gap-x-2">
            <RadioGroupItem id="wm" value="form4" />
            <Label for="wm">
            Waste Management
            </Label>
        </div>
        <div class="flex items-center gap-x-2">
            <RadioGroupItem id="its" value="it_service" />
            <Label for="its">
            IT Services
            </Label>
        </div>
        <div class="flex items-center gap-x-2">
            <RadioGroupItem id="os" value="form5" />
            <Label for="os">
            Other Services (specify)
            </Label>
        </div>
    </RadioGroup>

    <!-- Date and Time of Service -->
    <Label class="self-center">
        Date and Time of Service
    </Label>
    <div class="flex items-center gap-x-4">
        <Popover v-model:open="isDateOfServicePopoverOpen">
            <PopoverTrigger as-child>
            <Button
                type="button"
                variant="outline"
                class="w-[400px] ps-3 text-start font-normal"
            >
                <span>
                    {{ formatToDateString(serviceDate.toString()) }}
                </span>
                <Calendar class="ms-auto h-4 w-4 opacity-50" />
            </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
            <AppCalendar
                :model-value="serviceDate"
                @update:model-value="handleDateOfServiceChange"
            />
            </PopoverContent>
        </Popover>

        <Input
            id="time"
            type="time"
            v-model="serviceTime"
            class="w-[100px] bg-background appearance-none [&::-webkit-calendar-picker-indicator]:hidden"
            placeholder="Select a time"
        />
    </div>

    <!-- Client -->
    <Label for="client" class="self-center">
        Client
    </Label>
    <Input
        id="client"
        type="text"
        required
        placeholder="Enter client/company name"
        v-model="client"
        class="w-[400px]"
    />

    <!-- Address -->
    <Label for="address" class="self-start pt-1">
        Address
    </Label>
    <Textarea
        id="address"
        required
        placeholder="Enter client's complete address"
        v-model="address"
        class="w-full"
    />

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
    <!-- Department -->
        <div class="flex items-center gap-x-4">
            <Label for="department" class="w-48 shrink-0">
            Department/Branch
            </Label>
            <Input
            id="department"
            type="text"
            required
            placeholder="Enter client/company's department"
            v-model="department"
            class="w-[400px]"
            />
        </div>

        <div class="flex items-center">
            <Label for="position" class="w-36 shrink-0">
            Position
            </Label>
            <Input
            id="position"
            type="text"
            required
            placeholder="Enter contact's position"
            v-model="contactPosition"
            class="w-full"
            />
        </div>
    </div>

    <div class="col-span-2 grid grid-cols-2 gap-x-24">
        <div class="flex items-center gap-x-4">
            <Label for="contactPerson" class="w-48 shrink-0">
                Contact Person
            </Label>
            <Input
                id="contactPerson"
                type="text"
                required
                placeholder="Enter contact person"
                v-model="contactPerson"
                class="w-full"
            />
        </div>
        <div class="flex items-center">
            <Label for="contactNumber" class="w-36 shrink-0">
                Contact Number
            </Label>
            <Input
                id="contactNumber"
                type="text"
                minlength="11"
                maxlength="11"
                required
                placeholder="Enter contact person number"
                v-model="contactNumber"
                class="w-full"
            />
        </div>
    </div>
</template>
