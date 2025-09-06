import { useForm } from '@inertiajs/vue3'
import { ref, Ref } from 'vue'
import type { ITServiceFormProps } from '../types/types'

interface FormComponentInstance {
  validateForm(): boolean
  isValidForm: boolean
  errors: { [key: string]: string }
  showValidation: Ref<boolean>
}

export const useITServiceForm = (props: ITServiceFormProps) => {
  const formComponent = ref<FormComponentInstance | null>(null)

  const form = useForm({
    service_type: 'it_services',
    client: '',
    address: '',
    department: '',
    contact_no: '',
    contact_person: '',
    date: '',
    time: '',
    status: 'for check up',
    technician_id: '',
    machine_type: '',
    model: '',
    serial_no: '',
    tag_no: '',
    machine_problem: '',
  })

  const submitForm = () => {
    if (!formComponent.value) {
      return
    }

    if (formComponent.value.validateForm) {
      const isValid = formComponent.value.validateForm()

      if (!isValid) {
        setTimeout(() => {
          const firstError = document.querySelector(
            '.border-red-500',
          ) as HTMLElement
          if (firstError) {
            firstError.scrollIntoView({
              behavior: 'smooth',
              block: 'center',
            })
            if (
              firstError.tagName === 'INPUT' ||
              firstError.tagName === 'SELECT' ||
              firstError.tagName === 'TEXTAREA'
            ) {
              firstError.focus()
            }
          }
        }, 100)
        return
      }
    } else {
      return
    }

    // Submit form
    form.post(route('job_order.it_service.store'), {
      onSuccess: (response) => {
        form.reset()
        if (formComponent.value?.showValidation) {
          formComponent.value.showValidation.value = false
        }
      },
    })
  }

  return {
    form,
    formComponent,
    submitForm,
  }
}
