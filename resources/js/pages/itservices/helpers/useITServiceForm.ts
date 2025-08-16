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
    
    if (!formComponent.value) {
      console.error('Form component ref is null');
      return;
    }

    if (formComponent.value.validateForm) {
      console.log('Validating form...');
      const isValid = formComponent.value.validateForm();
      console.log('Form is valid:', isValid);

      if (!isValid) {
        console.log('Form validation failed');
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
      console.error('validateForm method not available');
      return;
    }

    console.log('Form data before submission:', form.data()); 
    console.log('Attempting to submit to route:', route('job_order.it_service.store')); 

    // Submit form
    form.post(route('job_order.it_service.store'), {
      onBefore: () => {
        console.log('Form submission started');
      },
      onStart: () => {
        console.log('Request started');
      },
      onProgress: () => {
        console.log('Upload progress');
      },
      onSuccess: (response) => {
        console.log('Form submission successful:', response);
        form.reset();
        if (formComponent.value?.showValidation) {
          formComponent.value.showValidation.value = false;
        }
        ;
      },
      onError: (errors) => {
        console.error('Form submission errors:', errors);
      },
      onFinish: () => {
        console.log('Form submission finished'); 
      }
    });
  };

  return {
    form,
    formComponent,
    submitForm,
  };
};