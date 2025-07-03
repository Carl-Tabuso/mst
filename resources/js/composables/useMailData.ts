import type { Mail } from '@/types/incident'
import axios from 'axios'
import { onMounted, ref } from 'vue'

export function useMailData() {
  const mails = ref<Mail[]>([])
  const loading = ref(false)
  const error = ref<Error | null>(null)
  const selectedMail = ref<string | null>(null)
  const selectedMails = ref<string[]>([])

  const SERVICE_TYPE_LABELS: Record<string, string[]> = {
    form4: ['Waste Management'],
    form5: ['Other Services'],
    it_service: ['IT Services'],
  }

  const DEFAULT_CREATOR = {
    id: 0,
    name: 'Unknown',
    email: '',
  }

  const transformIncidentToMail = (incident: any): Mail => {
    const serviceType = incident.job_order?.serviceable_type || ''
    const labels = SERVICE_TYPE_LABELS[serviceType] || []

    return {
      id: incident.id.toString(),
      subject: incident.subject,
      description: incident.description,
      html: incident.description,
      plainText: incident.plainText,
      is_read: incident.is_read,
      occured_at: incident.occured_at,
      location: incident.location,
      infraction_type: incident.infraction_type,
      status: incident.status,
      labels,
      created_by: incident.created_by || DEFAULT_CREATOR,
      involved_employees: incident.involved_employees || [],
      job_order: incident.job_order
        ? {
            id: incident.job_order.id,
            serviceable_type: incident.job_order.serviceable_type,
            status: incident.job_order.status,
          }
        : undefined,
    }
  }

  const fetchIncidents = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get('/incidents', {
        params: { with_trashed: false },
      })

      mails.value = response.data.map(transformIncidentToMail)

      selectedMail.value = mails.value.some(
        (mail) => mail.id === selectedMail.value,
      )
        ? selectedMail.value
        : null

      selectedMails.value = selectedMails.value.filter((id) =>
        mails.value.some((mail) => mail.id === id),
      )
    } catch (err) {
      console.error('Failed to fetch incidents:', err)
      error.value = err instanceof Error ? err : new Error(String(err))
    } finally {
      loading.value = false
    }
  }

  const archiveSelected = async () => {
    if (selectedMails.value.length === 0) return

    loading.value = true
    error.value = null

    try {
      await axios.post('/incidents/archive', {
        ids: selectedMails.value,
      })

      await fetchIncidents()
      selectedMails.value = []
    } catch (err) {
      console.error('Failed to archive:', err)
      error.value = err instanceof Error ? err : new Error(String(err))
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    fetchIncidents().catch((err) => {
      console.error('Error in onMounted:', err)
      error.value = err instanceof Error ? err : new Error(String(err))
    })
  })

  return {
    mails,
    loading,
    error,
    selectedMail,
    selectedMails,
    fetchIncidents,
    archiveSelected,
  }
}
