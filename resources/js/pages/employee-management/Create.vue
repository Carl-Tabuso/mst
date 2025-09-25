<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Separator } from '@/components/ui/separator'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import {
  CheckCircle2,
  Circle,
  CircleDashed,
  LoaderCircle,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'

interface Position {
  id: number
  name: string
}

interface Props {
  positions: Position[]
}

defineProps<Props>()

const openAccordions = ref<string[]>([
  'employee-info',
  'address',
  'emergency',
  'employment',
  'compensation',
])

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

const isEmployeeInfoComplete = computed(
  () =>
    form.last_name &&
    form.first_name &&
    form.date_of_birth &&
    form.email &&
    form.contact_number &&
    form.position_id,
)

const isAddressComplete = computed(
  () =>
    form.region &&
    form.province &&
    form.city &&
    form.zip_code &&
    form.detailed_address,
)

const isEmergencyComplete = computed(
  () =>
    form.emergency_last_name &&
    form.emergency_first_name &&
    form.emergency_contact_number &&
    form.emergency_relation,
)

const isEmploymentComplete = computed(() => form.date_hired)
const isCompensationComplete = computed(() => form.salary && form.allowance)

const getSectionIcon = (section: string) => {
  if (section === 'employee-info' && isEmployeeInfoComplete.value) {
    return CheckCircle2
  }
  if (section === 'address' && isAddressComplete.value) {
    return CheckCircle2
  }
  if (section === 'emergency' && isEmergencyComplete.value) {
    return CheckCircle2
  }
  if (section === 'employment' && isEmploymentComplete.value) {
    return CheckCircle2
  }
  if (section === 'compensation' && isCompensationComplete.value) {
    return CheckCircle2
  }

  return openAccordions.value.includes(section) ? Circle : CircleDashed
}

const getIconColor = (section: string) => {
  if (section === 'employee-info' && isEmployeeInfoComplete.value) {
    return 'text-green-500'
  }
  if (section === 'address' && isAddressComplete.value) {
    return 'text-green-500'
  }
  if (section === 'emergency' && isEmergencyComplete.value) {
    return 'text-green-500'
  }
  if (section === 'employment' && isEmploymentComplete.value) {
    return 'text-green-500'
  }
  if (section === 'compensation' && isCompensationComplete.value) {
    return 'text-green-500'
  }

  return openAccordions.value.includes(section)
    ? 'text-sky-900'
    : 'text-primary'
}

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
    allowance: 'Allowance is required',
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
    <div class="mx-auto w-full max-w-6xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-primary">
          Create Employee
        </h1>
        <p class="text-muted-foreground">Fill in the employee details below.</p>
      </div>

      <form
        @submit.prevent="onSubmit"
        class="w-full space-y-8"
      >
        <div class="w-full min-w-full">
          <Accordion
            type="multiple"
            v-model="openAccordions"
            class="w-full min-w-full divide-y divide-border rounded-lg border border-border bg-zinc-50 dark:bg-zinc-800"
          >
            <AccordionItem
              value="employee-info"
              class="w-full min-w-full px-4 py-4 sm:px-6"
            >
              <AccordionTrigger class="w-full py-2">
                <span
                  class="flex w-full items-center gap-2 text-sm font-bold text-primary"
                >
                  <component
                    :is="getSectionIcon('employee-info')"
                    class="h-5 w-5 shrink-0"
                    :class="getIconColor('employee-info')"
                  />
                  Employee Information
                </span>
              </AccordionTrigger>
              <AccordionContent class="w-full space-y-6 pt-4">
                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full">
                    <Label for="last_name"
                      >Last Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="last_name"
                      v-model="form.last_name"
                      class="w-full"
                    />
                    <InputError :message="form.errors.last_name" />
                  </div>
                  <div class="w-full">
                    <Label for="first_name"
                      >First Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="first_name"
                      v-model="form.first_name"
                      class="w-full"
                    />
                    <InputError :message="form.errors.first_name" />
                  </div>
                  <div class="w-full">
                    <Label for="middle_name">Middle Name</Label>
                    <Input
                      id="middle_name"
                      v-model="form.middle_name"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full">
                    <Label for="suffix">Suffix</Label>
                    <Input
                      id="suffix"
                      v-model="form.suffix"
                      class="w-full"
                    />
                  </div>
                </div>

                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full">
                    <Label for="date_of_birth"
                      >Date of Birth <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="date_of_birth"
                      type="date"
                      v-model="form.date_of_birth"
                      class="w-full"
                    />
                    <InputError :message="form.errors.date_of_birth" />
                  </div>
                  <div class="w-full">
                    <Label for="email"
                      >Email <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="email"
                      type="email"
                      v-model="form.email"
                      class="w-full"
                    />
                    <InputError :message="form.errors.email" />
                  </div>
                  <div class="w-full">
                    <Label for="contact_number"
                      >Contact Number <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="contact_number"
                      v-model="form.contact_number"
                      class="w-full"
                    />
                    <InputError :message="form.errors.contact_number" />
                  </div>
                  <div class="w-full">
                    <Label for="position_id"
                      >Position <span class="text-red-500">*</span></Label
                    >
                    <Select
                      v-model="form.position_id"
                      class="w-full"
                    >
                      <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select position" />
                      </SelectTrigger>
                      <SelectContent class="w-full">
                        <SelectItem
                          v-for="position in positions"
                          :key="position.id"
                          :value="position.id"
                          class="w-full"
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

            <AccordionItem
              value="address"
              class="w-full min-w-full px-4 py-4 sm:px-6"
            >
              <AccordionTrigger class="w-full py-2">
                <span
                  class="flex items-center gap-2 text-sm font-bold text-primary"
                >
                  <component
                    :is="getSectionIcon('address')"
                    class="h-5 w-5 shrink-0"
                    :class="getIconColor('address')"
                  />
                  Address
                </span>
              </AccordionTrigger>
              <AccordionContent class="w-full space-y-6 pt-4">
                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full">
                    <Label>Region <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.region"
                      class="w-full"
                    />
                    <InputError :message="form.errors.region" />
                  </div>
                  <div class="w-full">
                    <Label>Province <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.province"
                      class="w-full"
                    />
                    <InputError :message="form.errors.province" />
                  </div>
                  <div class="w-full">
                    <Label>City <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.city"
                      class="w-full"
                    />
                    <InputError :message="form.errors.city" />
                  </div>
                  <div class="w-full">
                    <Label>Zip Code <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.zip_code"
                      class="w-full"
                    />
                    <InputError :message="form.errors.zip_code" />
                  </div>
                </div>
                <div class="w-full">
                  <Label
                    >Detailed Address <span class="text-red-500">*</span></Label
                  >
                  <Textarea
                    v-model="form.detailed_address"
                    class="w-full"
                  />
                  <InputError :message="form.errors.detailed_address" />
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem
              value="emergency"
              class="w-full min-w-full px-4 py-4 sm:px-6"
            >
              <AccordionTrigger class="w-full py-2">
                <span
                  class="flex items-center gap-2 text-sm font-bold text-primary"
                >
                  <component
                    :is="getSectionIcon('emergency')"
                    class="h-5 w-5 shrink-0"
                    :class="getIconColor('emergency')"
                  />
                  Emergency Contact Person
                </span>
              </AccordionTrigger>
              <AccordionContent class="w-full space-y-6 pt-4">
                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full">
                    <Label>Last Name <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.emergency_last_name"
                      class="w-full"
                    />
                    <InputError :message="form.errors.emergency_last_name" />
                  </div>
                  <div class="w-full">
                    <Label
                      >First Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      v-model="form.emergency_first_name"
                      class="w-full"
                    />
                    <InputError :message="form.errors.emergency_first_name" />
                  </div>
                  <div class="w-full">
                    <Label>Middle Name</Label>
                    <Input
                      v-model="form.emergency_middle_name"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full">
                    <Label>Suffix</Label>
                    <Input
                      v-model="form.emergency_suffix"
                      class="w-full"
                    />
                  </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
                  <div class="w-full">
                    <Label
                      >Contact Number <span class="text-red-500">*</span></Label
                    >
                    <Input
                      v-model="form.emergency_contact_number"
                      class="w-full"
                    />
                    <InputError
                      :message="form.errors.emergency_contact_number"
                    />
                  </div>
                  <div class="w-full">
                    <Label>Relation <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.emergency_relation"
                      class="w-full"
                    />
                    <InputError :message="form.errors.emergency_relation" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem
              value="employment"
              class="w-full min-w-full px-4 py-4 sm:px-6"
            >
              <AccordionTrigger class="w-full py-2">
                <span
                  class="flex items-center gap-2 text-sm font-bold text-primary"
                >
                  <component
                    :is="getSectionIcon('employment')"
                    class="h-5 w-5 shrink-0"
                    :class="getIconColor('employment')"
                  />
                  Employment Details
                </span>
              </AccordionTrigger>
              <AccordionContent class="w-full space-y-6 pt-4">
                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full md:col-span-2">
                    <Label>SSS Number</Label>
                    <Input
                      v-model="form.sss_number"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>Pag-IBIG Number</Label>
                    <Input
                      v-model="form.pagibig_number"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>PhilHealth Number</Label>
                    <Input
                      v-model="form.philhealth_number"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>TIN</Label>
                    <Input
                      v-model="form.tin"
                      class="w-full"
                    />
                  </div>
                </div>

                <Separator class="w-full" />

                <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3">
                  <div class="w-full">
                    <Label
                      >Date Hired <span class="text-red-500">*</span></Label
                    >
                    <Input
                      type="date"
                      v-model="form.date_hired"
                      class="w-full"
                    />
                    <InputError :message="form.errors.date_hired" />
                  </div>
                  <div class="w-full">
                    <Label>Regularization Date</Label>
                    <Input
                      type="date"
                      v-model="form.regularization_date"
                      class="w-full"
                    />
                  </div>
                  <div class="w-full">
                    <Label>End of Contract</Label>
                    <Input
                      type="date"
                      v-model="form.end_of_contract"
                      class="w-full"
                    />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>

            <AccordionItem
              value="compensation"
              class="w-full min-w-full px-4 py-4 sm:px-6"
            >
              <AccordionTrigger class="w-full py-2">
                <span
                  class="flex items-center gap-2 text-sm font-bold text-primary"
                >
                  <component
                    :is="getSectionIcon('compensation')"
                    class="h-5 w-5 shrink-0"
                    :class="getIconColor('compensation')"
                  />
                  Compensation
                </span>
              </AccordionTrigger>
              <AccordionContent class="w-full space-y-6 pt-4">
                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full md:col-span-2">
                    <Label>Salary <span class="text-red-500">*</span></Label>
                    <Input
                      type="number"
                      step="0.01"
                      v-model="form.salary"
                      class="w-full"
                    />
                    <InputError :message="form.errors.salary" />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>Allowance <span class="text-red-500">*</span></Label>
                    <Input
                      type="number"
                      step="0.01"
                      v-model="form.allowance"
                      class="w-full"
                    />
                    <InputError :message="form.errors.allowance" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <div
          class="flex w-full flex-col justify-end gap-3 sm:flex-row sm:space-x-3"
        >
          <Button
            type="button"
            variant="outline"
            class="w-full sm:w-auto"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
            class="w-full sm:w-auto"
          >
            <LoaderCircle
              v-show="form.processing"
              class="mr-2 animate-spin"
            />
            Create Employee
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
