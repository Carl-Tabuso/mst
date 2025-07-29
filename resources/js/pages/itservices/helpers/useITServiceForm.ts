import { router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { ITServiceFormProps, Technician } from '../types/types'

export const useITServiceForm = (props: ITServiceFormProps) => {
  const form = useForm({
    client: '',
    address: '',
    department: '',
    contact_no: '',
    contact_person: '',
    date: '',
    time: '',
    status: 'for check up',
    technicians: [],
    machine_infos: [
      {
        machine_type: '',
        model: '',
        serial_no: '',
        tag_no: '',
        machine_problem: '',
        service_performed: '',
        recommendation: '',
        machine_status: '',
      },
    ],
  })

  const machineFieldsEnabled = computed(() => form.status === 'completed')

  const hasDuplicate = computed(() => {
    return form.machine_infos.some((machine) =>
      props.existingServices.some(
        (service) =>
          service.machine_type === machine.machine_type &&
          service.model.toLowerCase() === machine.model.toLowerCase() &&
          service.serial_no.toLowerCase() === machine.serial_no.toLowerCase() &&
          service.tag_no.toLowerCase() === machine.tag_no.toLowerCase(),
      ),
    )
  })

  const isValidMachine = computed(
    () =>
      form.machine_infos.every(
        (machine) =>
          machine.machine_type &&
          machine.model &&
          machine.serial_no?.length >= 3 &&
          machine.machine_status &&
          machine.tag_no,
      ) &&
      Array.isArray(form.technicians) &&
      form.technicians.length > 0 &&
      !hasDuplicate.value,
  )

  const addMachineInfo = () => {
    form.machine_infos.push({
      machine_type: '',
      model: '',
      serial_no: '',
      tag_no: '',
      machine_problem: '',
      service_performed: '',
      recommendation: '',
      machine_status: '',
    })
  }

  const submitForm = () => {
    const payload = {
      ...form.data(),
      technicians: form.technicians.map((t: Technician) => t.id),
    }

    console.log('Submitting IT Service payload:', payload)

    router.post(route('it-services.store'), payload, {
      onSuccess: () => {
        form.reset()
      },
    })
  }

  return {
    form,
    machineFieldsEnabled,
    hasDuplicate,
    isValidMachine,
    submitForm,
    addMachineInfo,
  }
}
