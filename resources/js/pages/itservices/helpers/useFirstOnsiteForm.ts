import { useForm, router } from '@inertiajs/vue3'
import { ref, Ref } from 'vue'

interface FormComponentInstance {
  validateForm(): boolean;
  isValidForm: boolean;
  errors: { [key: string]: string };
  showValidation: Ref<boolean>;
}

export const useFirstOnsiteForm = (jobOrderId: number, serviceId: number) => {
  const formComponent = ref<FormComponentInstance | null>(null);

  const form = useForm({
    onsite_type: 'initial',
    service_performed: '',
    recommendation: '',
    machine_status: '',
    job_order_id: jobOrderId,
    it_service_id: serviceId,
    attached_file: null,
  })

  const submitForm = () => {
    if (formComponent.value?.validateForm) {
      const isValid = formComponent.value.validateForm()
      
      if (!isValid) {
        setTimeout(() => {
          const firstError = document.querySelector('.border-red-500') as HTMLElement
          if (firstError) {
            firstError.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'center' 
            })
            if (firstError.tagName === 'INPUT' || 
                firstError.tagName === 'SELECT' || 
                firstError.tagName === 'TEXTAREA') {
              firstError.focus()
            }
          }
        }, 100)
        return
      }
    } else {
      return
    }

    router.post(
      route('job_order.it_service.onsite.first.store', jobOrderId),
      form.data(),
      {
        forceFormData: true,
        preserveState: false,
        onSuccess: () => {
          form.reset()
          if (formComponent.value?.showValidation) {
            formComponent.value.showValidation.value = false
          }
          
        },
      }
    )
  }

  return { 
    form, 
    formComponent,
    submitForm 
  }
}