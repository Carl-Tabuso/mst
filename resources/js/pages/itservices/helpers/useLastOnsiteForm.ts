import { useForm, router } from '@inertiajs/vue3'
import { computed, ref, Ref } from 'vue'

interface FormComponentInstance {
  validateForm(): boolean;
  isValidForm: boolean;
  errors: { [key: string]: string };
  showValidation: Ref<boolean>;
}

interface ExistingFinalReport {
  id?: number;
  service_performed?: string;
  parts_replaced?: string;
  final_remark?: string;
  machine_status?: string; // This is the key field for final_machine_status
  attached_file?: string;
}

export const useLastOnsiteForm = (
  jobOrderId: number, 
  serviceId: number, 
  existingFinalReport?: ExistingFinalReport
) => {
  const formComponent = ref<FormComponentInstance | null>(null);

  const form = useForm({
    onsite_type: 'final',
    service_performed: existingFinalReport?.service_performed || '',
    parts_replaced: existingFinalReport?.parts_replaced || '',
    final_remark: existingFinalReport?.final_remark || '',
    final_machine_status: existingFinalReport?.machine_status || '', // Initialize from existing data
    job_order_id: jobOrderId,
    it_service_id: serviceId,
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
            if (firstError.tagName === 'TEXTAREA' || firstError.tagName === 'SELECT') {
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
      route('job_order.it_service.onsite.last.store', jobOrderId),
      form.data(),
      {
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