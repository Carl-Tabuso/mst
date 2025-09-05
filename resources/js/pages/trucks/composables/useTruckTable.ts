import { useDebounceFn } from '@vueuse/core'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const urlParams = route().queryParams

const table = ref<Record<string, any>>({
  search: urlParams.search ?? '',
  page: Number(urlParams.page ?? 1),
  filters: {
    dispatchers: (urlParams.filters as any)?.dispatchers?.map((d: string) => Number(d)) ?? [],
  },
})

const url = route('truck.index')

const isProcessing = ref<boolean>(false)

export const useTruckTable = () => {
  const onDispatcherFilterSelect = (dispatcherId: number) => {
    const dispatcherFilter = table.value.filters.dispatchers

    if (dispatcherFilter.includes(dispatcherId)) {
      const index = dispatcherFilter.findIndex((d: number) => d === dispatcherId)

      dispatcherFilter.splice(index, 1)
    } else {
      dispatcherFilter.push(dispatcherId)
    }
    applyFilters()
  }

  const onClearDispatcherFilter = () => {
    table.value.filters.dispatchers = []
    applyFilters()
  }

  const onSearchInput = useDebounceFn(() => {
    router.get(url, table.value, {
      onStart: () => isProcessing.value = true,
      preserveState: true,
      replace: true,
      onFinish: () => isProcessing.value = false,
    })
  }, 500)

  const applyFilters = (options?: any) => {
    router.get(url, table.value, {
      ...options,
      onStart: () => isProcessing.value = true,
      preserveState: true,
      replace: true,
      onFinish: () => isProcessing.value = false,
    })
  }

  return {
    table,
    isProcessing,
    onDispatcherFilterSelect,
    onClearDispatcherFilter,
    onSearchInput,
    applyFilters,
  }
}
