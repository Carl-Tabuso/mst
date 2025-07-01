<script setup lang="ts">
import AppCalendar from '@/components/AppCalendar.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Separator } from '@/components/ui/separator'
import { Textarea } from '@/components/ui/textarea'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/vue3'
import { parseDate } from '@internationalized/date'
import { Calendar } from 'lucide-vue-next'
import { computed, ref } from 'vue'

const dateOfService = ref(new Date().toISOString())
const timeOfService = ref('')

const dos = computed(() => parseDate(dateOfService.value.split('T')[0]))

const form = useForm({
  service_type: 'form4',
  client: '',
  address: '',
  department: '',
  position: '',
  contact_person: '',
  contact_no: '',
})

const handleDateOfServiceChange = (value: any) => {
  dateOfService.value = new Date(value).toISOString()
}

const onSubmit = () => {
  const [hours, min] = timeOfService.value.split(':')
  const epoch = new Date(dateOfService.value).setHours(
    Number(hours),
    Number(min),
  )
  // found this abomination online. I need to format the date to Y-m-d H:i:s
  const formatted = new Date(epoch).toJSON().split('.')[0].split('T').join(' ')

  form.transform((data) => ({
    ...data,
    date_time: formatted,
  }))

  const path = jobOrderRouteNames.find((j) => j.id === form.service_type)

  form.post(route(`job_order.${path?.route}.store`))
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Job Orders',
    href: '/job-orders',
  },
  {
    title: 'List',
    href: '/job-orders',
  },
  {
    title: 'Create',
    href: '#',
  },
]
</script>

<template>
  <Head title="Create Job Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-3 py-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="mb-3 flex items-center">
          <div class="flex flex-col">
            <h3 class="mb-8 scroll-m-20 text-3xl font-bold">Add Job Order</h3>
            <form
              @submit.prevent="onSubmit"
              class="grid grid-cols-[auto,1fr] gap-x-12 gap-y-6"
            >
              <!-- Type of Service -->
              <Label class="self-center"> Type of Service </Label>
              <RadioGroup
                required
                v-model="form.service_type"
                class="flex items-center gap-x-10"
              >
                <div class="flex items-center gap-x-2">
                  <RadioGroupItem id="wm" value="form4" />
                  <Label for="wm"> Waste Management </Label>
                </div>
                <div class="flex items-center gap-x-2">
                  <RadioGroupItem id="its" value="it_service" />
                  <Label for="its"> IT Services </Label>
                </div>
                <div class="flex items-center gap-x-2">
                  <RadioGroupItem id="os" value="form5" />
                  <Label for="os"> Other Services (specify) </Label>
                </div>
              </RadioGroup>

              <!-- Date and Time of Service -->
              <Label class="self-center"> Date and Time of Service </Label>
              <div class="flex items-center gap-x-4">
                <Popover>
                  <PopoverTrigger as-child>
                    <Button
                      type="button"
                      variant="outline"
                      class="w-[300px] ps-3 text-start font-normal"
                    >
                      <span>
                        {{
                          new Date(dos.toString()).toLocaleDateString('en-ph', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          })
                        }}
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
                  class="w-[100px] appearance-none bg-background [&::-webkit-calendar-picker-indicator]:hidden"
                  placeholder="Select a time"
                />
              </div>

              <!-- Client -->
              <Label for="client" class="self-center"> Client </Label>
              <Input
                id="client"
                type="text"
                required
                placeholder="Enter client/company name"
                v-model="form.client"
                class="w-full"
              />

              <!-- Address -->
              <Label for="address" class="self-start pt-1"> Address </Label>
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
                  <Label for="position" class="w-36 shrink-0"> Position </Label>
                  <Input
                    id="position"
                    type="text"
                    required
                    placeholder="Enter contact's position"
                    v-model="form.position"
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

              <Separator class="col-[1/-1] my-4 w-full" />

              <!-- <Label for="address" class="self-start pt-1">
                Appraiser
              </Label>
              <Input
                id="appraisers"
                required
                placeholder="Enter client's complete address"
                v-model="form.address"
                class="w-full"
              /> -->

              <div class="col-[1/-1] flex w-full items-center">
                <div class="ml-auto space-x-3">
                  <Button type="button" variant="outline"> Cancel </Button>
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
