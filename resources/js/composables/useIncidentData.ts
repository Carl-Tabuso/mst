import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { Incident } from '@/types/incident'

export function useIncidentData() {
    const loading = ref(false)
    const incidents = ref<Incident[]>([])
    const selectedIncident = ref<string | null>(null)
    const selectedIncidents = ref<string[]>([])

    // Get incidents from page props
    const page = usePage()
    incidents.value = page.props.incidents || []

    const fetchIncidents = async (filters = {}) => {
        loading.value = true
        try {
            router.get(route('incidents.index'), filters, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    incidents.value = page.props.incidents || []
                }
            })
        } catch (error) {
            console.error('Failed to fetch incidents:', error)
        } finally {
            loading.value = false
        }
    }

    const archiveSelected = async () => {
        if (selectedIncidents.value.length === 0) return

        loading.value = true
        try {
            router.post(route('incidents.archive'), {
                ids: selectedIncidents.value
            }, {
                onSuccess: () => {
                    selectedIncidents.value = []
                    // Refresh the list
                    fetchIncidents()
                }
            })
        } catch (error) {
            console.error('Failed to archive:', error)
        } finally {
            loading.value = false
        }
    }

    const markAsRead = async (id: string) => {
        try {
            router.patch(route('incidents.markAsRead', { incident: id }))
        } catch (error) {
            console.error('Failed to mark as read:', error)
        }
    }

    return {
        incidents,
        loading,
        selectedIncident,
        selectedIncidents,
        fetchIncidents,
        archiveSelected,
        markAsRead
    }
}