import { ref } from 'vue'

export const useTruckTable = () => {
  const table = ref<Record<string, any>>({
    search: '',
    page: 1,
    per_page: 15,
    filters: {
      dispatchers: [],
    },
  })

  return {
    table,
  }
}
