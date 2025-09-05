<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { jobOrderRouteNames } from '@/constants/job-order-route'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { useForm, usePage } from '@inertiajs/vue3' 
import { LoaderCircle } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import FirstSection from './waste-managements/components/sections/FirstSection.vue'
import Form5Section from './other-services/components/Form5Section.vue' 

const page = usePage()
const employees = computed(() => page.props.employees || [])

const form = useForm({
  service_type: 'form4',
  date_time: new Date().toISOString(),
  client: '',
  address: '',
  department: '',
  contact_position: '',
  contact_person: '',
  contact_no: '',
  assigned_person: null,
  items: [],
  purpose: '', 
})

const timeOfService = ref('')

const isForm5 = computed(() => form.service_type === 'form5')

const onSubmit = () => {

  const [hours, min] = timeOfService.value.split(':')
  const epoch = new Date(form.date_time).setHours(Number(hours), Number(min))
  const formatted = new Date(epoch).toJSON().split('.')[0].split('T').join(' ')

  form.transform((data) => {
    const base = {
      ...data,
      date_time: formatted,
    }

    if (isForm5.value) {
      
      return {
        ...base,
        assigned_person: data.assigned_person,
        items: data.items,
        purpose: data.purpose,
      }
    }

    
    return {
      ...base,
      assigned_person: null,
      items: [],
    }
  })

  const path = jobOrderRouteNames.find((j) => j.id === form.service_type)

  form.post(route(`job_order.${path?.route}.store`))
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Job Orders', href: '/job-orders' },
  { title: 'List', href: '/job-orders' },
  { title: 'Create', href: '#' },
]
</script>

<template>
  <Head title="Create Job Order" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-3">
      <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div class="mb-3 flex items-center">
          <div class="flex flex-col">
            <h3 class="mb-8 scroll-m-20 text-3xl font-semibold tracking-tight">
              Add Job Order
            </h3>
            <form
              @submit.prevent="onSubmit"
              class="grid grid-cols-[auto,1fr] gap-y-6"
            >
              <div>
                <FirstSection
                  is-editing
                  v-model:serviceType="form.service_type"
                  v-model:serviceDate="form.date_time"
                  v-model:serviceTime="timeOfService"
                  v-model:client="form.client"
                  v-model:address="form.address"
                  v-model:department="form.department"
                  v-model:contactPosition="form.contact_position"
                  v-model:contactPerson="form.contact_person"
                  v-model:contactNumber="form.contact_no"
                />

                <Form5Section
                  v-if="isForm5"
                  v-model:assignedPerson="form.assigned_person"
                  v-model:items="form.items"
                  v-model:purpose="form.purpose"
                  :employees="employees"
                  :isEditing="true"
                />
              </div>

              <div class="col-[1/-1] flex w-full items-center">
                <div class="ml-auto space-x-3">
                  <Button
                    v-show="form.processing"
                    type="button"
                    variant="outline"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    variant="default"
                    :disabled="form.processing"
                  >
                    <LoaderCircle
                      v-show="form.processing"
                      class="animate-spin"
                    />
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
