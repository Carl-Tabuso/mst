import { router, usePage } from '@inertiajs/vue3'
import { computed, ref, nextTick } from 'vue'

interface JobOrder {
  id: number
  client: string
  contact_person?: string
  contact_no?: string
  address?: string
  serviceable_type?: string
  archived_at: string
  creator?: {
    first_name: string
    last_name: string
  }
  status?: 'available' | 'completed' | 'pending' | 'cancelled' | 'in-progress'
}

interface PaginatedJobOrders {
  data: JobOrder[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export function useArchivedJobOrders() {
  const page = usePage()
  
  const selectedIds = ref<number[]>([])
  const isLoading = ref(false)

  const archivedJobOrders = computed(() => page.props.archivedJobOrders as PaginatedJobOrders)
  
  const filters = computed(() => ({
    search: page.props.search || '',
    sort: page.props.sort || 'archived_at_desc',
  }))

  const pagination = computed(() => ({
    current_page: archivedJobOrders.value?.current_page || 1,
    last_page: archivedJobOrders.value?.last_page || 1,
    per_page: archivedJobOrders.value?.per_page || 10,
    total: archivedJobOrders.value?.total || 0,
    from: archivedJobOrders.value?.from || 0,
    to: archivedJobOrders.value?.to || 0,
  }))
  
  const isSelected = (id: number): boolean => selectedIds.value.includes(id)
  
  const toggleSelection = (id: number): void => {
    const index = selectedIds.value.indexOf(id)
    if (index > -1) {
      selectedIds.value.splice(index, 1)
    } else {
      selectedIds.value.push(id)
    }
  }
  
  const toggleSelectAll = (): void => {
    if (allSelected.value) {
      selectedIds.value = []
    } else {
      selectedIds.value = archivedJobOrders.value?.data?.map(job => job.id) || []
    }
  }
  
  const allSelected = computed((): boolean => {
    const data = archivedJobOrders.value?.data || []
    return data.length > 0 && data.every(job => selectedIds.value.includes(job.id))
  })

  const clearSelection = (): void => {
    selectedIds.value = []
  }

  // Enhanced search with debouncing handled at component level
  const handleSearch = (search: string): void => {
    if (isLoading.value) return

    router.get(
      route('archives.index'),
      {
        search: search.trim(),
        sort: filters.value.sort,
        page: 1,
      },
      { 
        preserveState: true, 
        preserveScroll: true,
        onStart: () => {
          isLoading.value = true
        },
        onFinish: () => {
          isLoading.value = false
        },
        onError: (errors) => {
          console.error('Search failed:', errors)
        }
      }
    )
  }

  // Enhanced sort
  const handleSort = (sortValue: string): void => {
    if (isLoading.value) return

    router.get(
      route('archives.index'),
      {
        search: filters.value.search,
        sort: sortValue,
        page: 1,
      },
      { 
        preserveState: true, 
        preserveScroll: true,
        onStart: () => {
          isLoading.value = true
        },
        onFinish: () => {
          isLoading.value = false
        }
      }
    )
  }

  // Handle per page changes
  const handlePerPageChange = (perPage: number): void => {
    if (isLoading.value) return

    router.get(
      route('archives.index'),
      {
        search: filters.value.search,
        sort: filters.value.sort,
        per_page: perPage,
        page: 1,
      },
      { 
        preserveState: true, 
        preserveScroll: true,
        onStart: () => {
          isLoading.value = true
        },
        onFinish: () => {
          isLoading.value = false
        }
      }
    )
  }

  // Enhanced restore
  const handleRestore = (): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value || selectedIds.value.length === 0) {
        reject(new Error('Invalid state for restore operation'))
        return
      }

      const idsToRestore = [...selectedIds.value]

      router.post(
        route('archives.restore'),
        { ids: idsToRestore },
        {
          preserveState: true,
          onStart: () => {
            isLoading.value = true
          },
          onSuccess: () => {
            clearSelection()
            resolve()
          },
          onError: (errors) => {
            console.error('Restore failed:', errors)
            reject(errors)
          },
          onFinish: () => {
            isLoading.value = false
          }
        }
      )
    })
  }

  // Enhanced force delete
  const handleForceDelete = (): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value || selectedIds.value.length === 0) {
        reject(new Error('Invalid state for delete operation'))
        return
      }

      const idsToDelete = [...selectedIds.value]

      router.post(
        route('archives.force-delete'),
        { ids: idsToDelete },
        {
          preserveState: true,
          onStart: () => {
            isLoading.value = true
          },
          onSuccess: () => {
            clearSelection()
            resolve()
          },
          onError: (errors) => {
            console.error('Delete failed:', errors)
            reject(errors)
          },
          onFinish: () => {
            isLoading.value = false
          }
        }
      )
    })
  }

  // Refresh functionality
  const handleRefresh = (): void => {
    if (isLoading.value) return

    router.reload({
      preserveState: true,
      preserveScroll: true,
      onStart: () => {
        isLoading.value = true
      },
      onFinish: () => {
        isLoading.value = false
      }
    })
  }

  // Enhanced date formatting
  const formatDate = (date: string | null): string => {
    if (!date) return 'Unknown'
    
    const deletedDate = new Date(date)
    const now = new Date()
    const diffInHours = Math.abs(now.getTime() - deletedDate.getTime()) / (1000 * 60 * 60)
    
    if (diffInHours < 24) {
      const diffInMinutes = Math.floor(diffInHours * 60)
      if (diffInMinutes < 60) {
        return `${diffInMinutes} minutes ago`
      }
      return `${Math.floor(diffInHours)} hours ago`
    }
    
    if (diffInHours < 24 * 7) {
      const diffInDays = Math.floor(diffInHours / 24)
      return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`
    }
    
    return deletedDate.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  // Separate date formatters for table display
  const formatDateOnly = (date: string | null): string => {
    if (!date) return 'Unknown'
    return new Date(date).toLocaleDateString('en-US', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    })
  }

  const formatTimeOnly = (date: string | null): string => {
    if (!date) return ''
    return new Date(date).toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  // Utility to get selection summary
  const getSelectionSummary = computed(() => {
    const count = selectedIds.value.length
    const total = archivedJobOrders.value?.data?.length || 0
    
    if (count === 0) return 'None selected'
    if (count === total) return 'All selected'
    return `${count} of ${total} selected`
  })

  // Bulk operations helper
  const canPerformBulkOperations = computed((): boolean => {
    return selectedIds.value.length > 0 && !isLoading.value
  })

  return {
    // Data
    archivedJobOrders,
    filters,
    selectedIds,
    pagination,
    isLoading,

    // Selection
    isSelected,
    toggleSelection,
    toggleSelectAll,
    allSelected,
    clearSelection,
    getSelectionSummary,

    // Actions
    handleSearch,
    handleSort,
    handleRestore,
    handleForceDelete,
    handleRefresh,
    handlePerPageChange,

    // Utilities
    formatDate,
    formatDateOnly,
    formatTimeOnly,
    canPerformBulkOperations,
  }
}