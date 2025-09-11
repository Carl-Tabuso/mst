<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Separator } from '@/components/ui/separator'
import InputError from '@/components/InputError.vue'
import { LoaderCircle, CheckCircle2, Pencil, X } from 'lucide-vue-next'
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
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
const page = usePage()

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
  regularizationDate: extractDate(props.employee.employmentDetails?.regularizationDate),
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
  form.emergencyContactNumber = props.employee.emergencyContact?.contactNumber || ''
  form.emergencyRelation = props.employee.emergencyContact?.relation || ''
  form.sssNumber = props.employee.employmentDetails?.sssNumber || ''
  form.philhealthNumber = props.employee.employmentDetails?.philhealthNumber || ''
  form.pagibigNumber = props.employee.employmentDetails?.pagibigNumber || ''
  form.tin = props.employee.employmentDetails?.tin || ''
  form.dateHired = extractDate(props.employee.employmentDetails?.dateHired)
  form.regularizationDate = extractDate(props.employee.employmentDetails?.regularizationDate)
  form.endOfContract = extractDate(props.employee.employmentDetails?.endOfContract)
  form.salary = props.employee.compensation?.salary || ''
  form.allowance = props.employee.compensation?.allowance || ''
}

const toggleEdit = () => {
  isEditing.value = !isEditing.value
  if (!isEditing.value) {
    resetForm()
  }
}

const onSubmit = () => {
  form.put(route('employee-management.update', props.employee.id), {
    onSuccess: () => {
      isEditing.value = false
      toast.success('Employee updated successfully')
    },
    onError: () => {
      toast.error('Error updating employee')
    }
  })
}

const isEmployeeInfoComplete = computed(() => 
  form.lastName && form.firstName && form.dateOfBirth && form.email && form.contactNumber && form.positionId
)

const isAddressComplete = computed(() => 
  form.region && form.province && form.city && form.zipCode && form.detailedAddress
)

const isEmergencyComplete = computed(() => 
  form.emergencyLastName && form.emergencyFirstName && form.emergencyContactNumber && form.emergencyRelation
)

const isEmploymentComplete = computed(() => form.dateHired)
const isCompensationComplete = computed(() => form.salary && form.allowance)
</script>

<template>
  <AppLayout>
    <div class="max-w-5xl mx-auto py-10 px-6 space-y-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Employee Details</h1>
          <p class="text-muted-foreground">
            {{ isEditing ? 'Edit employee information' : 'View employee details' }}
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
        <div v-else class="flex gap-2">
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
            <LoaderCircle v-show="form.processing" class="animate-spin mr-2" />
            Save Changes
          </Button>
        </div>
      </div>

      <form @submit.prevent="onSubmit" class="space-y-8">
        <Accordion
          type="multiple"
          :default-value="['employee-info', 'address', 'emergence', 'employment', 'compensation']"
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
                  <Label for="lastName">Last Name <span class="text-red-500">*</span></Label>
                  <Input 
                    id="lastName" 
                    v-model="form.lastName" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.lastName" />
                </div>
                <div>
                  <Label for="firstName">First Name <span class="text-red-500">*</span></Label>
                  <Input 
                    id="firstName" 
                    v-model="form.firstName" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.firstName" />
                </div>
                <div>
                  <Label for="middleName">Middle Name</Label>
                  <Input 
                    id="middleName" 
                    v-model="form.middleName" 
                    :disabled="!isEditing"
                  />
                </div>
                <div>
                  <Label for="suffix">Suffix</Label>
                  <Input 
                    id="suffix" 
                    v-model="form.suffix" 
                    :disabled="!isEditing"
                  />
                </div>
              </div>

              <div class="grid grid-cols-4 gap-4">
                <div>
                  <Label for="dateOfBirth">Date of Birth <span class="text-red-500">*</span></Label>
                  <Input 
                    id="dateOfBirth" 
                    type="date" 
                    v-model="form.dateOfBirth" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.dateOfBirth" />
                </div>
                <div>
                  <Label for="email">Email <span class="text-red-500">*</span></Label>
                  <Input 
                    id="email" 
                    type="email" 
                    v-model="form.email" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.email" />
                </div>
                <div>
                  <Label for="contactNumber">Contact Number <span class="text-red-500">*</span></Label>
                  <Input 
                    id="contactNumber" 
                    v-model="form.contactNumber" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.contactNumber" />
                </div>
                <div>
                  <Label for="positionId">Position <span class="text-red-500">*</span></Label>
                  <Select 
                    v-model="form.positionId" 
                    :disabled="!isEditing"
                  >
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
                  <InputError :message="form.errors.positionId" />
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
                  <Input 
                    v-model="form.region" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.region" />
                </div>
                <div>
                  <Label>Province <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.province" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.province" />
                </div>
                <div>
                  <Label>City <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.city" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.city" />
                </div>
                <div>
                  <Label>Zip Code <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.zipCode" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.zipCode" />
                </div>
              </div>
              <div>
                <Label>Detailed Address <span class="text-red-500">*</span></Label>
                <Textarea 
                  v-model="form.detailedAddress" 
                  :disabled="!isEditing"
                />
                <InputError :message="form.errors.detailedAddress" />
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
                  <Input 
                    v-model="form.emergencyLastName" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.emergencyLastName" />
                </div>
                <div>
                  <Label>First Name <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.emergencyFirstName" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.emergencyFirstName" />
                </div>
                <div>
                  <Label>Middle Name</Label>
                  <Input 
                    v-model="form.emergencyMiddleName" 
                    :disabled="!isEditing"
                  />
                </div>
                <div>
                  <Label>Suffix</Label>
                  <Input 
                    v-model="form.emergencySuffix" 
                    :disabled="!isEditing"
                  />
                </div>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <Label>Contact Number <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.emergencyContactNumber" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.emergencyContactNumber" />
                </div>
                <div>
                  <Label>Relation <span class="text-red-500">*</span></Label>
                  <Input 
                    v-model="form.emergencyRelation" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.emergencyRelation" />
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
                  <Input 
                    v-model="form.sssNumber" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.sssNumber" />
                </div>
                <div>
                  <Label>Pag-IBIG Number</Label>
                  <Input 
                    v-model="form.pagibigNumber" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.pagibigNumber" />
                </div>
                <div>
                  <Label>PhilHealth Number</Label>
                  <Input 
                    v-model="form.philhealthNumber" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.philhealthNumber" />
                </div>
                <div>
                  <Label>TIN</Label>
                  <Input 
                    v-model="form.tin" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.tin" />
                </div>
              </div>

              <Separator />

              <div class="grid grid-cols-3 gap-4">
                <div>
                  <Label>Date Hired <span class="text-red-500">*</span></Label>
                  <Input 
                    type="date" 
                    v-model="form.dateHired" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.dateHired" />
                </div>
                <div>
                  <Label>Regularization Date</Label>
                  <Input 
                    type="date" 
                    v-model="form.regularizationDate" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.regularizationDate" />
                </div>
                <div>
                  <Label>End of Contract</Label>
                  <Input 
                    type="date" 
                    v-model="form.endOfContract" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.endOfContract" />
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
                  <Input 
                    type="number" 
                    step="0.01" 
                    v-model="form.salary" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.salary" />
                </div>
                <div>
                  <Label>Allowance <span class="text-red-500">*</span></Label>
                  <Input 
                    type="number" 
                    step="0.01" 
                    v-model="form.allowance" 
                    :disabled="!isEditing"
                  />
                  <InputError :message="form.errors.allowance" />
                </div>
              </div>
            </AccordionContent>
          </AccordionItem>
        </Accordion>

        <div v-if="isEditing" class="flex justify-end space-x-3">
          <Button type="button" variant="outline" @click="toggleEdit">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            <LoaderCircle v-show="form.processing" class="animate-spin mr-2" />
            Save Changes
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>