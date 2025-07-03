import type {
  EmployeeSelection,
  FormData,
  JobOrderOption,
} from '@/types/incident'
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'

export function useIncidentForm() {
  const formData = ref<FormData>({
    subject: '',
    involvedEmployees: [],
    dateTime: '',
    location: '',
    infractionType: '',
    serviceType: '',
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

  const employees = ref<EmployeeSelection[]>([])
  const jobOrders = ref<JobOrderOption[]>([])
  const searchTerm = ref('')
  const selectedEmployeeIds = ref<number[]>([])

  const filteredEmployees = computed(() => {
    if (!employees.value) return []
    return employees.value
      .filter(
        (e) => !formData.value.involvedEmployees.some((sel) => sel.id === e.id),
      )
      .filter((e) =>
        searchTerm.value
          ? e.name.toLowerCase().includes(searchTerm.value.toLowerCase())
          : true,
      )
  })

  const employeeNames = computed({
    get: () => formData.value.involvedEmployees.map((e) => e.name),
    set: (names) => {
      const currentIds = new Set(
        formData.value.involvedEmployees.map((e) => e.id),
      )
      const newEmployees = names.map((name) => {
        const existing = formData.value.involvedEmployees.find(
          (e) => e.name === name,
        )
        if (existing) return existing
        const newEmp = employees.value.find((e) => e.name === name)
        return newEmp || { id: -1, name }
      })
      formData.value.involvedEmployees = newEmployees.filter((e) => e.id !== -1)
    },
  })

  const onJobOrderSelected = (value: string | number | null) => {
    if (!value) return
    const selected = jobOrders.value.find((j) => j.id === value)
    if (selected) {
      formData.value.serviceType = selected.service_type
    }
  }

  const handleEmployeeSelect = (employeeId: number) => {
    const employee = employees.value.find((e) => e.id === employeeId)
    if (
      employee &&
      !formData.value.involvedEmployees.some((e) => e.id === employeeId)
    ) {
      formData.value.involvedEmployees.push({
        id: employeeId,
        name: employee.name,
      })
      searchTerm.value = ''
    }
  }

  const addCurrentSearchTerm = () => {
    if (searchTerm.value.trim() && filteredEmployees.value.length === 1) {
      handleEmployeeSelect(filteredEmployees.value[0].id)
    }
  }

  const handleTagsUpdate = (newNames: string[]) => {
    formData.value.involvedEmployees = formData.value.involvedEmployees.filter(
      (emp) => newNames.includes(emp.name),
    )
  }

  const fetchData = async () => {
    try {
      const [empResponse, jobOrderResponse] = await Promise.all([
        axios.get('/data/employees/dropdown'),
        axios.get('/data/job-orders/dropdown'),
      ])

      employees.value = empResponse.data
      jobOrders.value = jobOrderResponse.data.map((j: any) => ({
        id: j.id,
        label: j.label,
        service_type: j.service_type,
      }))
    } catch (error) {
      console.error('Failed to load data:', error)
    }
  }

  onMounted(fetchData)

  watch(
    () => formData.value.involvedEmployees,
    (employees) => {
      selectedEmployeeIds.value = employees.map((e) => e.id)
    },
    { immediate: true },
  )

  return {
    formData,
    employees,
    jobOrders,
    infractionTypes,
    showOtherInfractionInput,
    searchTerm,
    filteredEmployees,
    employeeNames,
    selectedEmployeeIds,
    onJobOrderSelected,
    handleEmployeeSelect,
    addCurrentSearchTerm,
    handleTagsUpdate,
  }
}
