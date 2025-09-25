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
  Pencil,
  X,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { toast } from 'vue-sonner'

interface Position {
  id: number
  name: string
}

interface Employee {
  id: number
  firstName: string
  middleName?: string
  lastName: string
  suffix?: string
  dateOfBirth?: string
  email?: string
  contactNumber?: string
  positionId: number
  position?: Position
  region?: string
  province?: string
  city?: string
  zipCode?: string
  detailedAddress?: string
  emergencyContact?: {
    lastName: string
    firstName: string
    middleName?: string
    suffix?: string
    contactNumber: string
    relation: string
  }
  employmentDetails?: {
    sssNumber?: string
    philhealthNumber?: string
    pagibigNumber?: string
    tin?: string
    dateHired?: string
    regularizationDate?: string
    endOfContract?: string
  }
  compensation?: {
    salary?: number
    allowance?: number
  }
}

interface Props {
  employee: Employee
  positions: Position[]
}

const props = defineProps<Props>()
const isEditing = ref(false)

const openAccordions = ref<string[]>([
  'employee-info',
  'address',
  'emergency',
  'employment',
  'compensation',
])

const extractDate = (datetime: string | undefined): string => {
  if (!datetime) return ''
  if (datetime.includes('T')) {
    return datetime.split('T')[0]
  }
  return datetime
}

const form = useForm({
  lastName: props.employee.lastName,
  firstName: props.employee.firstName,
  middleName: props.employee.middleName || '',
  suffix: props.employee.suffix || '',
  dateOfBirth: extractDate(props.employee.dateOfBirth),
  email: props.employee.email || '',
  contactNumber: props.employee.contactNumber || '',
  positionId: props.employee.positionId,
  region: props.employee.region || '',
  province: props.employee.province || '',
  city: props.employee.city || '',
  zipCode: props.employee.zipCode || '',
  detailedAddress: props.employee.detailedAddress || '',
  emergencyLastName: props.employee.emergencyContact?.lastName || '',
  emergencyFirstName: props.employee.emergencyContact?.firstName || '',
  emergencyMiddleName: props.employee.emergencyContact?.middleName || '',
  emergencySuffix: props.employee.emergencyContact?.suffix || '',
  emergencyContactNumber: props.employee.emergencyContact?.contactNumber || '',
  emergencyRelation: props.employee.emergencyContact?.relation || '',
  sssNumber: props.employee.employmentDetails?.sssNumber || '',
  philhealthNumber: props.employee.employmentDetails?.philhealthNumber || '',
  pagibigNumber: props.employee.employmentDetails?.pagibigNumber || '',
  tin: props.employee.employmentDetails?.tin || '',
  dateHired: extractDate(props.employee.employmentDetails?.dateHired),
  regularizationDate: extractDate(
    props.employee.employmentDetails?.regularizationDate,
  ),
  endOfContract: extractDate(props.employee.employmentDetails?.endOfContract),
  salary: props.employee.compensation?.salary || '',
  allowance: props.employee.compensation?.allowance || '',
})

const resetForm = () => {
  form.lastName = props.employee.lastName
  form.firstName = props.employee.firstName
  form.middleName = props.employee.middleName || ''
  form.suffix = props.employee.suffix || ''
  form.dateOfBirth = extractDate(props.employee.dateOfBirth)
  form.email = props.employee.email || ''
  form.contactNumber = props.employee.contactNumber || ''
  form.positionId = props.employee.positionId
  form.region = props.employee.region || ''
  form.province = props.employee.province || ''
  form.city = props.employee.city || ''
  form.zipCode = props.employee.zipCode || ''
  form.detailedAddress = props.employee.detailedAddress || ''
  form.emergencyLastName = props.employee.emergencyContact?.lastName || ''
  form.emergencyFirstName = props.employee.emergencyContact?.firstName || ''
  form.emergencyMiddleName = props.employee.emergencyContact?.middleName || ''
  form.emergencySuffix = props.employee.emergencyContact?.suffix || ''
  form.emergencyContactNumber =
    props.employee.emergencyContact?.contactNumber || ''
  form.emergencyRelation = props.employee.emergencyContact?.relation || ''
  form.sssNumber = props.employee.employmentDetails?.sssNumber || ''
  form.philhealthNumber =
    props.employee.employmentDetails?.philhealthNumber || ''
  form.pagibigNumber = props.employee.employmentDetails?.pagibigNumber || ''
  form.tin = props.employee.employmentDetails?.tin || ''
  form.dateHired = extractDate(props.employee.employmentDetails?.dateHired)
  form.regularizationDate = extractDate(
    props.employee.employmentDetails?.regularizationDate,
  )
  form.endOfContract = extractDate(
    props.employee.employmentDetails?.endOfContract,
  )
  form.salary = props.employee.compensation?.salary || ''
  form.allowance = props.employee.compensation?.allowance || ''
}

const toggleEdit = () => {
  isEditing.value = !isEditing.value
  if (!isEditing.value) {
    resetForm()
  }
}

const validateForm = () => {
  form.clearErrors()

  const requiredFields = {
    lastName: 'Last name is required',
    firstName: 'First name is required',
    dateOfBirth: 'Date of birth is required',
    email: 'Email is required',
    contactNumber: 'Contact number is required',
    positionId: 'Position is required',
    region: 'Region is required',
    province: 'Province is required',
    city: 'City is required',
    zipCode: 'Zip code is required',
    detailedAddress: 'Detailed address is required',
    emergencyLastName: 'Emergency last name is required',
    emergencyFirstName: 'Emergency first name is required',
    emergencyContactNumber: 'Emergency contact number is required',
    emergencyRelation: 'Relation is required',
    dateHired: 'Date hired is required',
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

  form.put(route('employee-management.update', props.employee.id), {
    onSuccess: () => {
      isEditing.value = false
      toast.success('Employee updated successfully')
    },
    onError: () => {
      toast.error('Error updating employee')
    },
  })
}

const isEmployeeInfoComplete = computed(
  () =>
    form.lastName &&
    form.firstName &&
    form.dateOfBirth &&
    form.email &&
    form.contactNumber &&
    form.positionId,
)

const isAddressComplete = computed(
  () =>
    form.region &&
    form.province &&
    form.city &&
    form.zipCode &&
    form.detailedAddress,
)

const isEmergencyComplete = computed(
  () =>
    form.emergencyLastName &&
    form.emergencyFirstName &&
    form.emergencyContactNumber &&
    form.emergencyRelation,
)

const isEmploymentComplete = computed(() => form.dateHired)
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
</script>

<template>
  <AppLayout>
    <div class="mx-auto w-full max-w-6xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-primary">
            {{ isEditing ? 'Edit Employee' : 'Employee Details' }}
          </h1>
          <p class="text-muted-foreground">
            {{
              isEditing ? 'Edit employee information' : 'View employee details'
            }}
          </p>
        </div>
        <Button
          v-if="!isEditing"
          variant="outline"
          @click="toggleEdit"
        >
          <Pencil class="mr-2 h-4 w-4" />
          Edit Employee
        </Button>
        <div
          v-else
          class="flex gap-2"
        >
          <Button
            variant="outline"
            @click="toggleEdit"
          >
            <X class="mr-2 h-4 w-4" />
            Cancel
          </Button>
          <Button
            @click="onSubmit"
            :disabled="form.processing"
          >
            <LoaderCircle
              v-show="form.processing"
              class="mr-2 animate-spin"
            />
            Save Changes
          </Button>
        </div>
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
                    <Label for="lastName"
                      >Last Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="lastName"
                      v-model="form.lastName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.lastName" />
                  </div>
                  <div class="w-full">
                    <Label for="firstName"
                      >First Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="firstName"
                      v-model="form.firstName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.firstName" />
                  </div>
                  <div class="w-full">
                    <Label for="middleName">Middle Name</Label>
                    <Input
                      id="middleName"
                      v-model="form.middleName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full">
                    <Label for="suffix">Suffix</Label>
                    <Input
                      id="suffix"
                      v-model="form.suffix"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                </div>

                <div
                  class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                  <div class="w-full">
                    <Label for="dateOfBirth"
                      >Date of Birth <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="dateOfBirth"
                      type="date"
                      v-model="form.dateOfBirth"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.dateOfBirth" />
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
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.email" />
                  </div>
                  <div class="w-full">
                    <Label for="contactNumber"
                      >Contact Number <span class="text-red-500">*</span></Label
                    >
                    <Input
                      id="contactNumber"
                      v-model="form.contactNumber"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.contactNumber" />
                  </div>
                  <div class="w-full">
                    <Label for="positionId"
                      >Position <span class="text-red-500">*</span></Label
                    >
                    <Select
                      v-model="form.positionId"
                      class="w-full"
                      :disabled="!isEditing"
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
                    <InputError :message="form.errors.positionId" />
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
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.region" />
                  </div>
                  <div class="w-full">
                    <Label>Province <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.province"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.province" />
                  </div>
                  <div class="w-full">
                    <Label>City <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.city"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.city" />
                  </div>
                  <div class="w-full">
                    <Label>Zip Code <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.zipCode"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.zipCode" />
                  </div>
                </div>
                <div class="w-full">
                  <Label
                    >Detailed Address <span class="text-red-500">*</span></Label
                  >
                  <Textarea
                    v-model="form.detailedAddress"
                    class="w-full"
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.detailedAddress" />
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
                      v-model="form.emergencyLastName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.emergencyLastName" />
                  </div>
                  <div class="w-full">
                    <Label
                      >First Name <span class="text-red-500">*</span></Label
                    >
                    <Input
                      v-model="form.emergencyFirstName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.emergencyFirstName" />
                  </div>
                  <div class="w-full">
                    <Label>Middle Name</Label>
                    <Input
                      v-model="form.emergencyMiddleName"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full">
                    <Label>Suffix</Label>
                    <Input
                      v-model="form.emergencySuffix"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
                  <div class="w-full">
                    <Label
                      >Contact Number <span class="text-red-500">*</span></Label
                    >
                    <Input
                      v-model="form.emergencyContactNumber"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.emergencyContactNumber" />
                  </div>
                  <div class="w-full">
                    <Label>Relation <span class="text-red-500">*</span></Label>
                    <Input
                      v-model="form.emergencyRelation"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.emergencyRelation" />
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
                      v-model="form.sssNumber"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>Pag-IBIG Number</Label>
                    <Input
                      v-model="form.pagibigNumber"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>PhilHealth Number</Label>
                    <Input
                      v-model="form.philhealthNumber"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full md:col-span-2">
                    <Label>TIN</Label>
                    <Input
                      v-model="form.tin"
                      class="w-full"
                      :disabled="!isEditing"
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
                      v-model="form.dateHired"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.dateHired" />
                  </div>
                  <div class="w-full">
                    <Label>Regularization Date</Label>
                    <Input
                      type="date"
                      v-model="form.regularizationDate"
                      class="w-full"
                      :disabled="!isEditing"
                    />
                  </div>
                  <div class="w-full">
                    <Label>End of Contract</Label>
                    <Input
                      type="date"
                      v-model="form.endOfContract"
                      class="w-full"
                      :disabled="!isEditing"
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
                      :disabled="!isEditing"
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
                      :disabled="!isEditing"
                    />
                    <InputError :message="form.errors.allowance" />
                  </div>
                </div>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>

        <div
          v-if="isEditing"
          class="flex w-full flex-col justify-end gap-3 sm:flex-row sm:space-x-3"
        >
          <Button
            type="button"
            variant="outline"
            @click="toggleEdit"
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
            Save Changes
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
