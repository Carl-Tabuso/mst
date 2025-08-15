import { useForm, router } from '@inertiajs/vue3'
import { computed, ref, Ref } from 'vue'

// Define the component instance type
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
      console.log('[ERROR] No form component or validateForm method available')
      return
    }

    // Debug: Log form data before submission
    console.log('Form data before submission:', form.data())
    console.log('File:', form.attached_file)

    router.post(
      route('job_order.it_service.onsite.first.store', jobOrderId),
      form.data(),
      {
        forceFormData: true,
        preserveState: false, // Add this to ensure clean state
        onSuccess: () => {
          console.log('[SUCCESS] First onsite submitted successfully')
          form.reset()
          if (formComponent.value?.showValidation) {
            formComponent.value.showValidation.value = false
          }
          router.visit(route('job_order.it_service.index'))
        },
        onError: (errors: any) => {
          console.error('[ERROR] Validation failed:', errors)
          console.log('Current form state:', form.data())
        },
        onFinish: () => {
          console.log('[FINISH] Submission finished')
        }
      }
    )
  }

  return { 
    form, 
    formComponent,
    submitForm 
  }
}