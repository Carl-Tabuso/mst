import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

export function useArchivedJobOrders() {
  const page = usePage()

  const archivedJobOrders = computed(() => page.props.archivedJobOrders)
  const filters = computed(() => ({
    search: page.props.search || '',
  }))

  const pagination = computed(() => ({
    current_page: archivedJobOrders.value?.current_page || 1,
    last_page: archivedJobOrders.value?.last_page || 1,
    per_page: archivedJobOrders.value?.per_page || 10,
    total: archivedJobOrders.value?.total || 0,
  }))

  const selectedIds = ref<number[]>([])

  const isSelected = (id: number) => selectedIds.value.includes(id)
  const toggleSelection = (id: number) => {
    if (isSelected(id)) {
      selectedIds.value = selectedIds.value.filter((i) => i !== id)
    } else {
      selectedIds.value.push(id)
    }
  }
  const toggleSelectAll = () => {
    if (allSelected.value) {
      selectedIds.value = []
    } else {
      selectedIds.value = archivedJobOrders.value.data.map((job: any) => job.id)
    }
  }
  const allSelected = computed(() => {
    return archivedJobOrders.value.data.every((job: any) =>
      selectedIds.value.includes(job.id),
    )
  })

  const handleSearch = (search: string) => {
    router.get(
      route('archives.index'),
      {
        ...filters.value,
        search,
      },
      { preserveState: true, preserveScroll: true },
    )
  }

  const handleRestore = () => {
    router.post(
      route('archives.restore'),
      {
        ids: selectedIds.value,
      },
      {
        preserveState: true,
        onSuccess: () => (selectedIds.value = []),
      },
    )
  }

  const handleForceDelete = () => {
    router.post(
      route('archives.force-delete'),
      {
        ids: selectedIds.value,
      },
      {
        preserveState: true,
        onSuccess: () => (selectedIds.value = []),
      },
    )
  }

  const formatDate = (date: string | null) => {
    if (!date) return ''
    return new Date(date).toLocaleString()
  }

  return {
    archivedJobOrders,
    filters,
    selectedIds,
    isSelected,
    toggleSelection,
    toggleSelectAll,
    allSelected,
    formatDate,
    handleSearch,
    handleRestore,
    handleForceDelete,
    pagination,
  }
}
