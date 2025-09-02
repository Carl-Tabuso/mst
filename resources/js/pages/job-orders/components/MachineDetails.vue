<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea';
import { Employee } from '@/types';

interface MachineDetailsProps {
    errors?: any
}

defineProps<MachineDetailsProps>()

const machineType = defineModel<string>('machineType')
const machineModel = defineModel<string>('machineModel')
const serialNumber = defineModel<string>('serialNumber')
const tagNumber = defineModel<string>('tagNumber')
const machineProblem = defineModel<string>('machineProblem')
</script>

<template>
    <div class="mb-6">
        <div class="text-xl font-semibold leading-6">Machine Details</div>
        <p class="text-sm text-muted-foreground">
            General information of the requested service and client information.
        </p>
    </div>
    <div class="grid grid-cols-[auto,1fr] gap-x-7 gap-y-3">
        <div class="col-span-2 grid grid-cols-2 gap-x-10">
            <div class="flex flex-row items-start gap-x-4">
                <Label for="machineType" class="mt-3 w-44 shrink-0">
                    Machine Type
                </Label>
                <div class="flex w-full flex-col gap-1">
                    <Select v-model="machineType">
                        <SelectTrigger
                            id="machineType"
                            :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': errors?.machine_type }"
                        >
                            <SelectValue placeholder="Select a machine type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="(machineType, index) in ['Printer', 'Laptop', 'Desktop', 'Server', 'Other']"
                                :key="index"
                                :value="machineType"
                            >
                                {{ machineType }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors?.machine_type" />                    
                </div>
            </div>
            <div class="flex flex-row items-start">
                <Label for="machineModel" class="mt-3 w-36 shrink-0">
                    Model
                </Label>
                <div class="flex w-full flex-col gap-1">
                    <Input
                        id="machineModel"
                        type="text"
                        placeholder="Enter machine model"
                        :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': errors?.model }"
                        v-model="machineModel"
                    />
                    <InputError :message="errors?.model" />
                </div>
            </div>
        </div>
        <div class="col-span-2 grid grid-cols-2 gap-x-10">
            <div class="flex flex-row items-start gap-x-4">
                <Label for="serialNumber" class="mt-3 w-44 shrink-0">
                    Serial Number
                </Label>
                <div class="flex w-full flex-col gap-1">
                    <Input
                        id="serialNumber"
                        type="text"
                        placeholder="Enter machine serial number"
                        :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': errors?.serial_no }"
                        v-model="serialNumber"
                    />
                    <InputError :message="errors?.serial_no" />                   
                </div>
            </div> 
            <div class="flex flex-row items-start">
                <Label for="tagNumber" class="mt-3 w-36 shrink-0">
                    Tag Number
                </Label>
                <div class="flex w-full flex-col gap-1">
                    <Input
                        id="tagNumber"
                        type="text"
                        placeholder="Enter machine tag number"
                        :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': errors?.tag_no }"
                        v-model="tagNumber"
                    />
                    <InputError :message="errors?.tag_no" />  
                </div>
            </div>
            <div class="flex col-span-2 flex-row mt-3 items-center">
                <Label for="problem" class="w-48 shrink-0 self-start pt-1">
                    Machine Problem
                </Label>
                <div class="flex flex-col w-full gap-1">
                    <Textarea
                        id="problem"
                        placeholder="Describe the machine problem"
                        :class="['w-full', {
                            'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': errors?.machine_problem
                        }]"
                        v-model="machineProblem"
                    />  
                    <InputError :message="errors?.machine_problem" />                       
                </div>
            </div>
        </div>
    </div>
</template>
