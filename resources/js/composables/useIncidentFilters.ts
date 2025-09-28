import type { Incident, IncidentFilters } from '@/types/incident'
import { computed, ref, type Ref } from 'vue'

export function useIncidentFilters(incidents: Ref<Incident[]>) {
  const searchValue = ref('')
  const activeTab = ref('All')
  const sortBy = ref('recent')
  const tempFilters = ref<IncidentFilters>({
    statuses: [],
    dateFrom: '',
    dateTo: '',
  })
  const activeFilters = ref<IncidentFilters>({
    statuses: [],
    dateFrom: '',
    dateTo: '',
  })

  const applyFilters = () => {
    activeFilters.value = {
      statuses: [...tempFilters.value.statuses],
      dateFrom: tempFilters.value.dateFrom,
      dateTo: tempFilters.value.dateTo,
    }
  }

  const clearFilters = () => {
    tempFilters.value = {
      statuses: [],
      dateFrom: '',
      dateTo: '',
    }
    applyFilters()
  }

  const filteredIncidentList = computed(() => {
    let output = [...incidents.value]

    if (searchValue.value.trim()) {
      const searchTerm = searchValue.value.toLowerCase()
      output = output.filter(
        (item) =>
          item.subject.toLowerCase().includes(searchTerm) ||
          item.plainText.toLowerCase().includes(searchTerm) ||
          item.location?.toLowerCase().includes(searchTerm) ||
          item.infraction_type?.toLowerCase().includes(searchTerm) ||
          item.involved_employees?.some((emp) =>
            emp.name.toLowerCase().includes(searchTerm),
          ),
      )
    }

    if (activeTab.value !== 'All') {
      output = output.filter((item) => {
        if (!item.job_order) return false

        if (activeTab.value === 'Waste Management') {
          return item.job_order.serviceable_type === 'form4'
        } else if (activeTab.value === 'IT Services') {
          return item.job_order.serviceable_type === 'it_service'
        } else if (activeTab.value === 'Other Services') {
          return item.job_order.serviceable_type === 'form5'
        }
        return true
      })
    }

    if (activeFilters.value.statuses.length > 0) {
      output = output.filter((item) =>
        activeFilters.value.statuses.includes(item.status),
      )
    }

    if (activeFilters.value.dateFrom || activeFilters.value.dateTo) {
      output = output.filter((item) => {
        const itemDate = new Date(item.occured_at)
        const fromDate = activeFilters.value.dateFrom
          ? new Date(activeFilters.value.dateFrom)
          : null
        const toDate = activeFilters.value.dateTo
          ? new Date(activeFilters.value.dateTo)
          : null

        return (
          (!fromDate || itemDate >= fromDate) && (!toDate || itemDate <= toDate)
        )
      })
    }
    if (sortBy.value === 'recent') {
      output.sort(
        (a, b) =>
          new Date(b.occured_at).getTime() - new Date(a.occured_at).getTime(),
      )
    } else if (sortBy.value === 'asc') {
      output.sort((a, b) => a.subject.localeCompare(b.subject))
    } else if (sortBy.value === 'desc') {
      output.sort((a, b) => b.subject.localeCompare(a.subject))
    }

    return output
  })

  return {
    searchValue,
    activeTab,
    sortBy,
    tempFilters,
    activeFilters,
    filteredIncidentList,
    applyFilters,
    clearFilters,
  }
}
