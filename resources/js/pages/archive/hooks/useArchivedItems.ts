import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface ArchivedItem {
  id: number
  type: 'job_order' | 'user' | 'employee' | 'correction'
  name: string
  display_name: string
  archived_at: string
  archived_by_name: string
  archived_by_position: string
  model_data: any
}

interface PaginatedArchivedItems {
  data: ArchivedItem[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

interface SelectedItem {
  id: number
  type: string
}

export function useArchivedItems() {
  const page = usePage()
  
  const selectedItems = ref<SelectedItem[]>([])
  const isLoading = ref(false)

  const archivedItems = computed(() => page.props.archivedItems as PaginatedArchivedItems)
  
  const filters = computed(() => ({
    search: page.props.search || '',
    type: page.props.typeFilter || '',
    sort: page.props.sort || 'archived_at_desc',
  }))

  const pagination = computed(() => ({
    current_page: archivedItems.value?.current_page || 1,
    last_page: archivedItems.value?.last_page || 1,
    per_page: archivedItems.value?.per_page || 10,
    total: archivedItems.value?.total || 0,
    from: archivedItems.value?.from || 0,
    to: archivedItems.value?.to || 0,
  }))

  // Type display mapping
  const getTypeDisplay = (type: string): string => {
    const typeMap = {
      job_order: 'Job Order',
      user: 'User',
      employee: 'Employee',
      correction: 'Correction',
    }
    return typeMap[type as keyof typeof typeMap] || type
  }

  const getTypeBadgeClass = (type: string): string => {
    const colorMap = {
      job_order: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
      user: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      employee: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
      correction: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
    }
    return colorMap[type as keyof typeof colorMap] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
  }
  
  const isSelected = (id: number, type: string): boolean => {
    return selectedItems.value.some(item => item.id === id && item.type === type)
  }
  
  const toggleSelection = (id: number, type: string): void => {
    const index = selectedItems.value.findIndex(item => item.id === id && item.type === type)
    if (index > -1) {
      selectedItems.value.splice(index, 1)
    } else {
      selectedItems.value.push({ id, type })
    }
  }
  
  const toggleSelectAll = (): void => {
    if (allSelected.value) {
      selectedItems.value = []
    } else {
      selectedItems.value = archivedItems.value?.data?.map(item => ({ 
        id: item.id, 
        type: item.type 
      })) || []
    }
  }
  
  const allSelected = computed((): boolean => {
    const data = archivedItems.value?.data || []
    return data.length > 0 && data.every(item => 
      selectedItems.value.some(selected => selected.id === item.id && selected.type === item.type)
    )
  })

  const clearSelection = (): void => {
    selectedItems.value = []
  }

  // Group selections by type for bulk operations
  const selectionsByType = computed(() => {
    const groups: Record<string, number[]> = {}
    selectedItems.value.forEach(item => {
      if (!groups[item.type]) {
        groups[item.type] = []
      }
      groups[item.type].push(item.id)
    })
    return groups
  })

  // Enhanced search with debouncing handled at component level
  const handleSearch = (search: string): void => {
    if (isLoading.value) return

    router.get(
      route('archives.index'),
      {
        search: search.trim(),
        type: filters.value.type,
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

  // Type filter
  const handleTypeFilter = (type: string): void => {
    if (isLoading.value) return

    router.get(
      route('archives.index'),
      {
        search: filters.value.search,
        type: type,
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
        type: filters.value.type,
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
        type: filters.value.type,
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

  // Enhanced restore - handles multiple types
  const handleRestore = (): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value || selectedItems.value.length === 0) {
        reject(new Error('Invalid state for restore operation'))
        return
      }

      const promises: Promise<void>[] = []

      // Process each type separately
      Object.entries(selectionsByType.value).forEach(([type, ids]) => {
        const promise = new Promise<void>((resolve, reject) => {
          router.post(
            route('archives.restore'),
            { ids, type },
            {
              preserveState: true,
              onSuccess: () => resolve(),
              onError: (errors) => reject(errors),
            }
          )
        })
        promises.push(promise)
      })

      // Execute all restores
      isLoading.value = true
      Promise.all(promises)
        .then(() => {
          clearSelection()
          resolve()
        })
        .catch((errors) => {
          console.error('Restore failed:', errors)
          reject(errors)
        })
        .finally(() => {
          isLoading.value = false
        })
    })
  }

  // Enhanced force delete - handles multiple types
  const handleForceDelete = (): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value || selectedItems.value.length === 0) {
        reject(new Error('Invalid state for delete operation'))
        return
      }

      const promises: Promise<void>[] = []

      // Process each type separately
      Object.entries(selectionsByType.value).forEach(([type, ids]) => {
        const promise = new Promise<void>((resolve, reject) => {
          router.post(
            route('archives.force-delete'),
            { ids, type },
            {
              preserveState: true,
              onSuccess: () => resolve(),
              onError: (errors) => reject(errors),
            }
          )
        })
        promises.push(promise)
      })

      // Execute all deletes
      isLoading.value = true
      Promise.all(promises)
        .then(() => {
          clearSelection()
          resolve()
        })
        .catch((errors) => {
          console.error('Delete failed:', errors)
          reject(errors)
        })
        .finally(() => {
          isLoading.value = false
        })
    })
  }

  // Single item restore
  const handleSingleRestore = (id: number, type: string): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value) {
        reject(new Error('Operation in progress'))
        return
      }

      isLoading.value = true
      router.post(
        route('archives.restore'),
        { ids: [id], type },
        {
          preserveState: true,
          onSuccess: () => resolve(),
          onError: (errors) => {
            console.error('Single restore failed:', errors)
            reject(errors)
          },
          onFinish: () => {
            isLoading.value = false
          }
        }
      )
    })
  }

  // Single item force delete
  const handleSingleForceDelete = (id: number, type: string): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (isLoading.value) {
        reject(new Error('Operation in progress'))
        return
      }

      isLoading.value = true
      router.post(
        route('archives.force-delete'),
        { ids: [id], type },
        {
          preserveState: true,
          onSuccess: () => resolve(),
          onError: (errors) => {
            console.error('Single delete failed:', errors)
            reject(errors)
          },
          onFinish: () => {
            isLoading.value = false
          }
        }
      )
    })
  }

  const handleRefresh = (): void => {
    if (isLoading.value) return

    router.reload({
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
    const count = selectedItems.value.length
    const total = archivedItems.value?.data?.length || 0
    
    if (count === 0) return 'None selected'
    if (count === total) return 'All selected'
    return `${count} of ${total} selected`
  })

  // Bulk operations helper
  const canPerformBulkOperations = computed((): boolean => {
    return selectedItems.value.length > 0 && !isLoading.value
  })

  return {
    // Data
    archivedItems,
    filters,
    selectedItems,
    pagination,
    isLoading,

    // Selection
    isSelected,
    toggleSelection,
    toggleSelectAll,
    allSelected,
    clearSelection,
    getSelectionSummary,
    selectionsByType,

    // Actions
    handleSearch,
    handleTypeFilter,
    handleSort,
    handleRestore,
    handleForceDelete,
    handleSingleRestore,
    handleSingleForceDelete,
    handleRefresh,
    handlePerPageChange,

    // Utilities
    formatDate,
    formatDateOnly,
    formatTimeOnly,
    getTypeDisplay,
    getTypeBadgeClass,
    canPerformBulkOperations,
  }
}