import { computed, ref, Ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import type { ITServiceFormProps} from '../types/types';

interface FormComponentInstance {
  validateForm(): boolean;
  isValidForm: boolean;
  errors: { [key: string]: string };
  showValidation: Ref<boolean>;
}

export const useITServiceForm = (props: ITServiceFormProps) => {
  const formComponent = ref<FormComponentInstance | null>(null);

  const form = useForm({
    service_type: 'it_services',
    client: '',
    address: '',
    department: '',
    position: '',
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
  });

  const submitForm = () => {
    
    if (formComponent.value?.validateForm) {

      const isValid = formComponent.value.validateForm();

      if (!isValid) {
        setTimeout(() => {
          const firstError = document.querySelector('.border-red-500') as HTMLElement;
          if (firstError) {
            firstError.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'center' 
            });
            if (firstError.tagName === 'INPUT' || 
                firstError.tagName === 'SELECT' || 
                firstError.tagName === 'TEXTAREA') {
              firstError.focus();
            }
          }
        }, 100);
        return;
      }
    } else {
      console.log('No form component or validateForm method available');
      return;
    }

    router.post(route('job_order.it_service.store'), form.data(), {
      onSuccess: () => {
        form.reset();
        if (formComponent.value?.showValidation) {
          formComponent.value.showValidation.value = false;
        }
        router.visit(route('job_order.it_service.index'))
      },
      onError: (errors: any) => {
        console.error('Form submission errors:', errors);
      }
    });
  };

  return {
    form,
    formComponent,
    submitForm,
  };
};