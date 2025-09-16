import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import type { Incident } from '@/types/incident'

export function useIncidentData() {
    const loading = ref(false)
    const incidents = ref<Incident[]>([])
    const selectedIncident = ref<string | null>(null)
    const selectedIncidents = ref<string[]>([])

    const page = usePage()
    
    if (page.props.incidents) {
        incidents.value = page.props.incidents
    }

    const fetchIncidents = async (filters = {}) => {
        loading.value = true
        
        try {
            router.get(route('incidents.index'), filters, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    incidents.value = page.props.incidents || []
                }
            })
        } catch (error) {
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
                    fetchIncidents()
                }
            })
        } catch (error) {
        } finally {
            loading.value = false
        }
    }

    const markAsRead = async (id: string) => {
        try {
            router.patch(route('incidents.markAsRead', { incident: id }), {})
        } catch (error) {
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