<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { FormControl, FormDescription, FormField, FormItem, FormLabel } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { machineStatuses } from '@/constants/machine-statuses';
import AppLayout from '@/layouts/AppLayout.vue';
import TicketHeader from '@/pages/job-orders/components/TicketHeader.vue';
import { ITService } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface InitialOnsiteFormProps {
    itService: ITService
}

const props = defineProps<InitialOnsiteFormProps>()

const form = useForm({
    service_performed: '',
    recommendation: '',
    machine_status: '',
    report_file: null as any,
})

const onSubmit = () => {
    form.post(route('job_order.it_service.onsite.initial.store' , props.itService.id), {
        onStart: () => form.clearErrors(),
        forceFormData: true,
        preserveState: true,
        replace: true,
    })
}
</script>

<template>
    <Head :title="itService.jobOrder.ticket" />
    <AppLayout>
        <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
            <div class="border-b border-border bg-background shadow-sm">
                <div class="flex flex-row items-center mb-3 justify-between">
                    <TicketHeader :job-order="itService.jobOrder" />
                </div>
            </div>
            <div class="mt-4">
                <div class="mb-6">
                    <div>
                        <div class="text-xl text-foreground font-semibold leading-6">
                            First Onsite Visit
                        </div>
                        <p class="text-sm text-muted-foreground">
                            The initial onsite report details.
                        </p>
                    </div>
                </div>
                <form
                    @submit.prevent="onSubmit"
                    class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6 mt-4"
                    enctype="multipart/form-data"
                >
                    <!-- <div class="col-span-2">
                        <FormField name="servicesPerformed">
                            <FormItem>
                                <FormLabel>
                                    Services Performed
                                </FormLabel>
                                <FormControl>
                                    <Textarea
                                        id="servicesPerformed"
                                        v-model="form.service_performed"
                                        placeholder="Describe the services performed in detail"
                                        class="w-full"
                                    />
                                </FormControl>
                                <FormDescription>
                                    The detailed performed services.
                                </FormDescription>
                            </FormItem>
                        </FormField>
                    </div> -->
                    <div class="col-span-2">
                        <div class="flex items-start">
                            <Label for="servicesPerformed" class="mt-3 w-44 shrink-0">
                                Services Performed
                            </Label>
                            <div class="flex flex-col gap-1 w-full">
                                <Textarea
                                    id="servicesPerformed"
                                    v-model="form.service_performed"
                                    placeholder="Describe the services performed in detail"
                                    :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': form.errors.service_performed }"
                                /> 
                                <InputError :message="form.errors.service_performed" />                               
                            </div>
                        </div>                    
                    </div>
                    <div class="col-span-2 flex flex-row items-start">
                        <Label for="recommendation" class="mt-3 w-44 shrink-0">
                            Recommendation
                        </Label>
                        <div class="flex flex-col w-full gap-1">
                            <Textarea
                                id="recommendation"
                                v-model="form.recommendation"
                                placeholder="Provide technician's recommendation"
                                :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': form.errors.recommendation }"
                            />                            
                            <InputError :message="form.errors.recommendation" />
                        </div>
                    </div>
                    <div class="col-span-2 grid grid-cols-2 gap-x-10">
                        <div class="flex flex-row items-start">
                            <Label for="reportFile" class="mt-3 w-44 shrink-0">
                                Report File
                            </Label>
                            <div class="flex flex-col w-full gap-1">
                                <Input
                                    type="file"
                                    :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': form.errors.report_file }"
                                    @input="form.report_file = $event.target.files[0]"
                                />
                                <template v-if="form.errors.report_file">
                                    <InputError :message="form.errors.report_file" />                             
                                </template>
                                <p
                                    v-else
                                    class="text-xs text-muted-foreground"
                                >
                                    Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-row items-start">
                            <Label for="machineStatus" class="mt-3 w-36 shrink-0">
                                Machine Status
                            </Label>
                            <div class="flex flex-col w-full gap-1">
                                <Select v-model="form.machine_status">
                                    <SelectTrigger
                                        id="machineStatus"
                                        :class="{ 'focus border-destructive focus-visible:ring-0 focus-visible:ring-destructive': form.errors.machine_status }"
                                    >
                                        <SelectValue placeholder="Select current machine status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="({ id, label }) in machineStatuses"
                                            :key="id"
                                            :value="id"
                                        >
                                            {{ label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>           
                                <InputError :message="form.errors.machine_status" />                      
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 flex flex-row items-center gap-3 justify-end">
                        <Button
                            type="button"
                            variant="outline"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            <LoaderCircle
                                v-if="form.processing"
                                class="animate-spin"
                            />
                            Submit Report
                        </Button>
                    </div>                    
                </form>
            </div>
        </div>
    </AppLayout>
</template>
