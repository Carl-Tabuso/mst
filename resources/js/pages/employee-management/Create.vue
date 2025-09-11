  <script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue'
  import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
  import { Button } from '@/components/ui/button'
  import { Input } from '@/components/ui/input'
  import { Label } from '@/components/ui/label'
  import { Textarea } from '@/components/ui/textarea'
  import { Separator } from '@/components/ui/separator'
  import InputError from '@/components/InputError.vue'
  import { LoaderCircle, CheckCircle2 } from 'lucide-vue-next'
  import { useForm } from '@inertiajs/vue3'
  import { computed } from 'vue'
  import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
  } from '@/components/ui/select'

  interface Position {
    id: number
    name: string
  }

  interface Props {
    positions: Position[]
  }

  defineProps<Props>()

  const form = useForm({
    last_name: '',
    first_name: '',
    middle_name: '',
    suffix: '',
    date_of_birth: '',
    email: '',
    contact_number: '',
    position_id: null as number | null,
    region: '',
    province: '',
    city: '',
    zip_code: '',
    detailed_address: '',
    emergency_last_name: '',
    emergency_first_name: '',
    emergency_middle_name: '',
    emergency_suffix: '',
    emergency_contact_number: '',
    emergency_relation: '',
    sss_number: '',
    philhealth_number: '',
    pagibig_number: '',
    tin: '',
    date_hired: '',
    regularization_date: '',
    end_of_contract: '',
    salary: '',
    allowance: '',
  })

  const isEmployeeInfoComplete = computed(() => 
    form.last_name && form.first_name && form.date_of_birth && form.email && form.contact_number && form.position_id
  )

  const isAddressComplete = computed(() => 
    form.region && form.province && form.city && form.zip_code && form.detailed_address
  )

  const isEmergencyComplete = computed(() => 
    form.emergency_last_name && form.emergency_first_name && form.emergency_contact_number && form.emergency_relation
  )

  const isEmploymentComplete = computed(() => form.date_hired)
  const isCompensationComplete = computed(() => form.salary && form.allowance)

  const validateForm = () => {
    form.clearErrors()
    
    const requiredFields = {
      last_name: 'Last name is required',
      first_name: 'First name is required',
      date_of_birth: 'Date of birth is required',
      email: 'Email is required',
      contact_number: 'Contact number is required',
      position_id: 'Position is required',
      region: 'Region is required',
      province: 'Province is required',
      city: 'City is required',
      zip_code: 'Zip code is required',
      detailed_address: 'Detailed address is required',
      emergency_last_name: 'Emergency last name is required',
      emergency_first_name: 'Emergency first name is required',
      emergency_contact_number: 'Emergency contact number is required',
      emergency_relation: 'Relation is required',
      date_hired: 'Date hired is required',
      salary: 'Salary is required',
      allowance: 'Allowance is required'
    }

    const errors: Record<string, string> = {}
    
    Object.entries(requiredFields).forEach(([field, message]) => {
      if (!form[field as keyof typeof form]) {
        errors[field] = message
      }
    })

    if (Object.keys(errors).length > 0) {
      form.setError(errors)
      return false
    }
    
    return true
  }

  const onSubmit = () => {
    if (!validateForm()) return
    form.post(route('employee-management.store'))
  }
  </script>

  <template>
    <AppLayout>
      <div class="max-w-5xl mx-auto py-10 px-6 space-y-8">
        <div>
          <h1 class="text-2xl font-bold">Create Employee</h1>
          <p class="text-muted-foreground">Fill in the employee details below.</p>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-8">
          <Accordion
            type="multiple"
            :default-value="['employee-info', 'address', 'emergency', 'employment', 'compensation']"
            class="divide-y divide-border rounded-lg border border-border bg-zinc-50"
          >
            <AccordionItem value="employee-info" class="px-6 py-4">
              <AccordionTrigger class="py-2">
                <span class="flex items-center gap-2 text-lg font-semibold">
                  <CheckCircle2 v-if="isEmployeeInfoComplete" class="text-green-500 w-5 h-5 shrink-0" />
                  Employee Information
                </span>
              </AccordionTrigger>
              <AccordionContent class="pt-4 space-y-6">
                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <Label for="last_name">Last Name <span class="text-red-500">*</span></Label>
                    <Input id="last_name" v-model="form.last_name" />
                    <InputError :message="form.errors.last_name" />
                  </div>
                  <div>
                    <Label for="first_name">First Name <span class="text-red-500">*</span></Label>
                    <Input id="first_name" v-model="form.first_name" />
                    <InputError :message="form.errors.first_name" />
                  </div>
                  <div>
                    <Label for="middle_name">Middle Name</Label>
                    <Input id="middle_name" v-model="form.middle_name" />
                  </div>
                  <div>
                    <Label for="suffix">Suffix</Label>
                    <Input id="suffix" v-model="form.suffix" />
                  </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <Label for="date_of_birth">Date of Birth <span class="text-red-500">*</span></Label>
                    <Input id="date_of_birth" type="date" v-model="form.date_of_birth" />
                    <InputError :message="form.errors.date_of_birth" />
                  </div>
                  <div>
                    <Label for="email">Email <span class="text-red-500">*</span></Label>
                    <Input id="email" type="email" v-model="form.email" />
                    <InputError :message="form.errors.email" />
                  </div>
                  <div>
                    <Label for="contact_number">Contact Number <span class="text-red-500">*</span></Label>
                    <Input id="contact_number" v-model="form.contact_number" />
                    <InputError :message="form.errors.contact_number" />
                  </div>
                  <div>
                    <Label for="position_id">Position <span class="text-red-500">*</span></Label>
                    <Select v-model="form.position_id">
                      <SelectTrigger>
                        <SelectValue placeholder="Select position" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem 
                          v-for="position in positions" 
                          :key="position.id" 
                          :value="position.id"
                        >
                          {{ position.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                    <InputError :message="form.errors.position_id" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="address" class="px-6 py-4">
              <AccordionTrigger class="py-2">
                <span class="flex items-center gap-2 text-lg font-semibold">
                  <CheckCircle2 v-if="isAddressComplete" class="text-green-500 w-5 h-5 shrink-0" />
                  Address
                </span>
              </AccordionTrigger>
              <AccordionContent class="pt-4 space-y-6">
                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <Label>Region <span class="text-red-500">*</span></Label>
                    <Input v-model="form.region" />
                    <InputError :message="form.errors.region" />
                  </div>
                  <div>
                    <Label>Province <span class="text-red-500">*</span></Label>
                    <Input v-model="form.province" />
                    <InputError :message="form.errors.province" />
                  </div>
                  <div>
                    <Label>City <span class="text-red-500">*</span></Label>
                    <Input v-model="form.city" />
                    <InputError :message="form.errors.city" />
                  </div>
                  <div>
                    <Label>Zip Code <span class="text-red-500">*</span></Label>
                    <Input v-model="form.zip_code" />
                    <InputError :message="form.errors.zip_code" />
                  </div>
                </div>
                <div>
                  <Label>Detailed Address <span class="text-red-500">*</span></Label>
                  <Textarea v-model="form.detailed_address" />
                  <InputError :message="form.errors.detailed_address" />
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="emergency" class="px-6 py-4">
              <AccordionTrigger class="py-2">
                <span class="flex items-center gap-2 text-lg font-semibold">
                  <CheckCircle2 v-if="isEmergencyComplete" class="text-green-500 w-5 h-5 shrink-0" />
                  Emergency Contact Person
                </span>
              </AccordionTrigger>
              <AccordionContent class="pt-4 space-y-6">
                <div class="grid grid-cols-4 gap-4">
                  <div>
                    <Label>Last Name <span class="text-red-500">*</span></Label>
                    <Input v-model="form.emergency_last_name" />
                    <InputError :message="form.errors.emergency_last_name" />
                  </div>
                  <div>
                    <Label>First Name <span class="text-red-500">*</span></Label>
                    <Input v-model="form.emergency_first_name" />
                    <InputError :message="form.errors.emergency_first_name" />
                  </div>
                  <div>
                    <Label>Middle Name</Label>
                    <Input v-model="form.emergency_middle_name" />
                  </div>
                  <div>
                    <Label>Suffix</Label>
                    <Input v-model="form.emergency_suffix" />
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Contact Number <span class="text-red-500">*</span></Label>
                    <Input v-model="form.emergency_contact_number" />
                    <InputError :message="form.errors.emergency_contact_number" />
                  </div>
                  <div>
                    <Label>Relation <span class="text-red-500">*</span></Label>
                    <Input v-model="form.emergency_relation" />
                    <InputError :message="form.errors.emergency_relation" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="employment" class="px-6 py-4">
              <AccordionTrigger class="py-2">
                <span class="flex items-center gap-2 text-lg font-semibold">
                  <CheckCircle2 v-if="isEmploymentComplete" class="text-green-500 w-5 h-5 shrink-0" />
                  Employment Details
                </span>
              </AccordionTrigger>
              <AccordionContent class="pt-4 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label>SSS Number</Label>
                    <Input v-model="form.sss_number" />
                  </div>
                  <div>
                    <Label>Pag-IBIG Number</Label>
                    <Input v-model="form.pagibig_number" />
                  </div>
                  <div>
                    <Label>PhilHealth Number</Label>
                    <Input v-model="form.philhealth_number" />
                  </div>
                  <div>
                    <Label>TIN</Label>
                    <Input v-model="form.tin" />
                  </div>
                </div>

                <Separator />

                <div class="grid grid-cols-3 gap-4">
                  <div>
                    <Label>Date Hired <span class="text-red-500">*</span></Label>
                    <Input type="date" v-model="form.date_hired" />
                    <InputError :message="form.errors.date_hired" />
                  </div>
                  <div>
                    <Label>Regularization Date</Label>
                    <Input type="date" v-model="form.regularization_date" />
                  </div>
                  <div>
                    <Label>End of Contract</Label>
                    <Input type="date" v-model="form.end_of_contract" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem value="compensation" class="px-6 py-4">
              <AccordionTrigger class="py-2">
                <span class="flex items-center gap-2 text-lg font-semibold">
                  <CheckCircle2 v-if="isCompensationComplete" class="text-green-500 w-5 h-5 shrink-0" />
                  Compensation
                </span>
              </AccordionTrigger>
              <AccordionContent class="pt-4 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <Label>Salary <span class="text-red-500">*</span></Label>
                    <Input type="number" step="0.01" v-model="form.salary" />
                    <InputError :message="form.errors.salary" />
                  </div>
                  <div>
                    <Label>Allowance <span class="text-red-500">*</span></Label>
                    <Input type="number" step="0.01" v-model="form.allowance" />
                    <InputError :message="form.errors.allowance" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>

          <div class="flex justify-end space-x-3">
            <Button type="button" variant="outline">Cancel</Button>
            <Button type="submit" :disabled="form.processing">
              <LoaderCircle v-show="form.processing" class="animate-spin mr-2" />
              Create Employee
            </Button>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>