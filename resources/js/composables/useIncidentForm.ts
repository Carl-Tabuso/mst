import type { FormData } from '@/types/incident'
import { ref } from 'vue'

export function useIncidentForm() {
  const formData = ref<FormData>({
    subject: '',
    involvedEmployees: [],
    dateTime: '',
    location: '',
    infractionType: '',
    description: '',
    jobOrder: null,
  })

  const showOtherInfractionInput = ref(false)
  const infractionTypes = ref([
    'Safety Violation',
    'Equipment Damage',
    'Unauthorized Access',
    'Policy Breach',
    'Others',
  ])

  const loadIncidentData = (incident: any) => {
    if (!incident || !incident.id) {
      clearForm()
      return
    }

    clearForm()

    const jobOrderId =
      incident.hauling_job_order?.id ||
      incident.job_order?.id ||
      incident.job_order_id ||
      null

      const jobOrderTicket =
    incident.hauling_job_order?.ticket ||
    incident.job_order?.ticket ||
    null

    let involvedEmployees: any[] = []

    if (incident.haulers && incident.haulers.length > 0) {
      involvedEmployees = incident.haulers.map((hauler: any) => ({
        id: hauler.id,
        name: hauler.name,
      }))
    }

    if (incident.involved_employees && incident.involved_employees.length > 0) {
      involvedEmployees = [
        ...involvedEmployees,
        ...incident.involved_employees.map((emp: any) => ({
          id: emp.id,
          name: emp.name,
        })),
      ]
    }

    const formatDateTimeForInput = (dateString: string) => {
      if (!dateString) return new Date().toISOString().slice(0, 16)

      try {
        const date = new Date(dateString)
        if (isNaN(date.getTime())) {
          return new Date().toISOString().slice(0, 16)
        }

        const offset = date.getTimezoneOffset() * 60000
        const localDate = new Date(date.getTime() - offset)
        return localDate.toISOString().slice(0, 16)
      } catch {
        return new Date().toISOString().slice(0, 16)
      }
    }

    formData.value = {
      jobOrder: jobOrderId,
       jobOrderTicket: jobOrderTicket,
      subject: incident.subject || '',
      location: incident.location || '',
      infractionType: incident.infraction_type || '',
      dateTime: formatDateTimeForInput(incident.occured_at),
      description: incident.description || '',
      involvedEmployees: involvedEmployees,
    }
  }

  const clearForm = () => {
    formData.value = {
      subject: '',
      involvedEmployees: [],
      dateTime: new Date().toISOString().slice(0, 16),
      location: '',
      infractionType: '',
      description: '',
      jobOrder: null,
    }
  }

  return {
    formData,
    infractionTypes,
    showOtherInfractionInput,
    loadIncidentData,
    clearForm,
  }
}
