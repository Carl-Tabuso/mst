<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Employee, Form4 } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import { ref, computed } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Calendar, ChevronDown, CheckIcon } from 'lucide-vue-next'
import AppCalendar from '@/components/AppCalendar.vue'
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover'
import { Textarea } from '@/components/ui/textarea'
import { Separator } from '@/components/ui/separator'
import { parseDate } from '@internationalized/date'
import { 
  Command, 
  CommandEmpty, 
  CommandGroup, 
  CommandInput, 
  CommandItem, 
  CommandList 
} from '@/components/ui/command'
import { Badge } from '@/components/ui/badge'

const props = defineProps<{
    form4: Form4
    employees: { data: Employee[] }
}>()

const { form4, employees } = props

const { form3, jobOrder } = form4

const { data: employeeData } = employees

const existingAppraisers = ref<Employee[]>(form4?.appraisers)

const form = useForm({
  service_type: jobOrder.serviceableType,
  date_time: '',
  client: jobOrder.client,
  address: jobOrder.address,
  department: jobOrder.department,
  contact_position: jobOrder.contactPosition,
  contact_person: jobOrder.contactPerson,
  contact_no: jobOrder.contactNo,
  payment_date: form4?.paymentDate,
  payment_type: form3?.paymentType,
  bid_bond: form4?.bidBond,
  or_number: form4?.orNumber,
  status: jobOrder.status,
  appraised_date: form3?.appraisedDate,
  approved_date: form3?.approvedDate,
  team_leader: form3?.teamLeader,
  team_driver: form3?.teamDriver,
  safety_officer: form3?.safetyOfficer,
  team_mechanic: form3?.teamMechanic
})

const formatToDateString = (date: string) => {
  return new Date(date).toLocaleDateString('en-ph', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const dateOfService = ref(new Date(jobOrder.dateTime).toISOString())
const dateAppraised = ref(new Date(form3?.appraisedDate || new Date()).toISOString())
const paymentDate = ref(new Date(form4?.paymentDate || new Date()).toISOString())
const approvedDate = ref(new Date(form3?.approvedDate || new Date()).toISOString())
const timeOfService = ref('')

const dos = computed(() => parseDate(dateOfService.value.split('T')[0]))
const da = computed(() => parseDate(dateAppraised.value.split('T')[0]))
const pd = computed(() => parseDate(paymentDate.value.split('T')[0]))
const ad = computed(() => parseDate(approvedDate.value.split('T')[0]))

const dateType = {
  dos: 'dateOfService',
  da: 'dateAppraised',
  pd: 'paymentDate',
  ad: 'approvedDate',
}

const handleDateChange = (type: keyof typeof dateType, value: any) => {
  // type.value = new Date(value).toISOString()
}

const handleDateOfServiceChange = (value: any) => {
  dateOfService.value = new Date(value).toISOString()
}

const handleAppraisedDateChange = (value: any) => {
  dateAppraised.value = new Date(value).toISOString()
}

const handlePaymentDateChange = (value: any) => {
  paymentDate.value = new Date(value).toISOString()
}

const handleApprovedDateChange = (value: any) => {
  approvedDate.value = new Date(value).toISOString()
}

const onSubmit = () => {
  //
}

const isExistingAppraiser = (employeeId: number) => {
  return existingAppraisers.value.map((a) => a.id).includes(employeeId)
}

const handleIsSelected = (employee: Employee) => {
  if (isExistingAppraiser(employee.id)) {
    const index = existingAppraisers.value.findIndex((a) => a.id === employee.id)
    
    existingAppraisers.value.splice(index, 1)
  } else {
    existingAppraisers.value.push(employee)
  }
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders'
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: 'Edit',
    href: '#'
  }
]
</script>

<template>
  <Head title="Edit Job Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-3 py-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="flex items-center mb-3">
          <div class="flex flex-col">
            <h3 class="scroll-m-20 text-3xl font-bold mb-8">
              Edit Job Order
            </h3>
            <form @submit.prevent="onSubmit" class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6">
              <!-- Type of Service -->
              <Label class="self-center">
                Type of Service
              </Label>
              <RadioGroup
                required
                v-model="form.service_type" 
                class="flex items-center gap-x-10">
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
                <Popover>
                  <PopoverTrigger as-child>
                    <Button
                      type="button"
                      variant="outline"
                      class="w-[300px] ps-3 text-start font-normal"
                    >
                      <span>
                        {{ formatToDateString(dos.toString()) }}
                      </span>
                      <Calendar class="ms-auto h-4 w-4 opacity-50" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent class="w-auto p-0">
                    <AppCalendar
                      :max-value="dos"
                      :model-value="dos"
                      @update:model-value="handleDateOfServiceChange"
                    />
                  </PopoverContent>
                </Popover>

                <Input
                  id="time"
                  type="time"
                  v-model="timeOfService"
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
                v-model="form.client"
                class="w-full"
              />

              <!-- Address -->
              <Label for="address" class="self-start pt-1">
                Address
              </Label>
              <Textarea
                id="address"
                required
                placeholder="Enter client's complete address"
                v-model="form.address"
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
                    v-model="form.department"
                    class="w-[300px]"
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
                    v-model="form.contact_position"
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
                    v-model="form.contact_person"
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
                    v-model="form.contact_no"
                    class="w-full"
                  />
                </div>
              </div>

              <Separator class="my-2 w-full col-[1/-1]" />

              <div class="col-span-2 grid grid-cols-2 gap-x-24">
                <div class="flex items-center gap-x-4">
                  <Label for="appraisers" class="w-48 shrink-0">
                    Appraisers
                  </Label>
                  <Popover>
                    <PopoverTrigger class="w-[400px]" as-child>
                      <Button variant="outline" class="">
                        <template v-if="existingAppraisers.length > 0">
                          <Badge
                            v-if="existingAppraisers.length <= 2"
                            v-for="appraiser in existingAppraisers.slice(0, 2)"
                            :key="appraiser.id"
                            class="rounded-full font-normal truncate overflow-hidden text-ellipsis"
                            variant="secondary"
                          >{{ appraiser.fullName }}
                          </Badge>
                          <template v-else>
                            <Badge
                              v-for="appraiser in existingAppraisers.slice(0, 1)"
                              :key="appraiser.id"
                              class="rounded-full font-normal"
                              variant="secondary"
                            >{{ `${appraiser.fullName} and ${existingAppraisers.length - 1} more` }}
                            </Badge>
                          </template>
                        </template>
                        <template v-else>
                          Select appraisers
                        </template>
                        <ChevronDown class="ml-2 h-4 w-4" />
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
                              v-for="employee in employeeData"
                              :key="employee.id"
                              :value="employee"
                              @select="() => handleIsSelected(employee)"
                              >
                              <div
                                :class="cn(
                                  'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                                  isExistingAppraiser(employee.id)
                                    ? 'bg-primary text-primary-foreground'
                                    : 'opacity-50 [&_svg]:invisible',
                                )"
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
                  <Popover>
                    <PopoverTrigger as-child>
                      <Button
                        type="button"
                        variant="outline"
                        class="w-full ps-3 text-start font-normal"
                      >
                        <span>
                          {{ formatToDateString(da.toString()) }}
                        </span>
                        <Calendar class="ms-auto h-4 w-4 opacity-50" />
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                      <AppCalendar
                        :max-value="da"
                        :model-value="da"
                        @update:model-value="handleAppraisedDateChange"
                      />
                    </PopoverContent>
                  </Popover>                  
                </div>
              </div>

              <Separator class="my-2 w-full col-[1/-1]" />

              <div class="col-span-2 grid grid-cols-2 gap-x-24">
                <div class="flex items-center gap-x-4">
                  <Label for="paymentType" class="w-48 shrink-0">
                    Type of Payment
                  </Label>
                  <Input
                    id="paymentType"
                    required
                    placeholder="Enter client's payment type"
                    v-model="form.payment_type"
                    class="w-full"
                  />
                </div>
                <div class="flex items-center">
                  <Label for="bidBond" class="w-48 shrink-0">
                    Bid Bond
                  </Label>
                  <Input
                    id="bidBond"
                    required
                    placeholder="Enter job order's bid bond"
                    v-model="form.bid_bond"
                    class="w-full"
                  />
                </div>
              </div>

              <div class="col-span-2 grid grid-cols-2 gap-x-24">
                <div class="flex items-center gap-x-4">
                  <Label for="orNumber" class="w-48 shrink-0">
                    OR Number
                  </Label>
                  <Input
                    id="orNumber"
                    required
                    placeholder="Enter OR Number"
                    v-model="form.or_number"
                    class="w-full"
                  />
                </div>
                <div class="flex items-center">
                  <Label for="paymentDate" class="w-48 shrink-0">
                    Date of Payment
                  </Label>
                  <Popover>
                    <PopoverTrigger as-child>
                      <Button
                        type="button"
                        variant="outline"
                        class="w-full ps-3 text-start font-normal"
                      >
                        <span>
                          {{ formatToDateString(da.toString()) }}
                        </span>
                        <Calendar class="ms-auto h-4 w-4 opacity-50" />
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                      <AppCalendar
                        :max-value="pd"
                        :model-value="pd"
                        @update:model-value="handlePaymentDateChange"
                      />
                    </PopoverContent>
                  </Popover> 
                </div>
              </div>

              <div class="col-span-2 grid grid-cols-2 gap-x-24">
                <div class="flex items-center gap-x-4">
                  <Label for="approvedDate" class="w-48 shrink-0">
                    Date Approved
                  </Label>
                  <Popover>
                    <PopoverTrigger as-child>
                      <Button
                        type="button"
                        variant="outline"
                        class="w-full ps-3 text-start font-normal"
                      >
                        <span>
                          {{ formatToDateString(ad.toString()) }}
                        </span>
                        <Calendar class="ms-auto h-4 w-4 opacity-50" />
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0">
                      <AppCalendar
                        :max-value="ad"
                        :model-value="ad"
                        @update:model-value="handleApprovedDateChange"
                      />
                    </PopoverContent>
                  </Popover> 
                </div>
                <div class="flex items-center">
                  <Label for="status" class="w-48 shrink-0">
                    Status
                  </Label>
                  <Input
                    id="status"
                    required
                    placeholder="Enter OR Number"
                    v-model="form.status"
                    class="w-full"
                  />
                </div>
              </div>

              <Separator class="my-2 w-full col-[1/-1]" />

              <div class="w-full col-[1/-1] flex items-center">
                <div class="ml-auto space-x-3">
                  <Button type="button" variant="outline">
                    Cancel
                  </Button>
                  <Button type="submit" variant="default">
                    Add Job Order
                  </Button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>    
  </AppLayout>
</template>
