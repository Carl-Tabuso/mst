import type {
  EmployeeSelection,
  FormData,
  JobOrderOption,
} from '@/types/incident'
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

export function useIncidentForm() {
  const page = usePage()
  
  // Get data from page props (pre-loaded by Laravel)
  const employees = ref<EmployeeSelection[]>(page.props.employees || [])
  const jobOrders = ref<JobOrderOption[]>(page.props.jobOrders || [])

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

  const searchTerm = ref('')
  const selectedEmployeeIds = ref<number[]>([])

  const filteredEmployees = computed(() => {
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
      formData.value.jobOrder = selected.id
    }
  }

  // NEW: Load incident data into form
  const loadIncidentData = (incident: any) => {
    if (!incident) return
    
    // Determine which job order to use (regular or hauling)
    const jobOrderId = incident.job_order?.id || incident.hauling_job_order?.id;
    const serviceType = incident.job_order?.service_type || incident.hauling_job_order?.service_type || '';
    
    // Use assigned personnel from hauling or involved employees
    const involvedEmployees = incident.assigned_personnel?.length ? 
                            incident.assigned_personnel : 
                            incident.involved_employees;

    formData.value = {
      jobOrder: jobOrderId || null,
      subject: incident.subject || '',
      location: incident.location || '',
      infractionType: incident.infraction_type || '',
      dateTime: incident.occured_at ? 
               new Date(incident.occured_at).toISOString().slice(0, 16) : 
               new Date().toISOString().slice(0, 16),
      description: incident.description || '',
      serviceType: serviceType,
      involvedEmployees: involvedEmployees || []
    };
    
    // Update selected employee IDs
    updateSelectedEmployeeIds();
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
      updateSelectedEmployeeIds()
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
    updateSelectedEmployeeIds()
  }

  const updateSelectedEmployeeIds = () => {
    selectedEmployeeIds.value = formData.value.involvedEmployees.map((e) => e.id)
  }

  const clearForm = () => {
    formData.value = {
      subject: '',
      involvedEmployees: [],
      dateTime: new Date().toISOString().slice(0, 16),
      location: '',
      infractionType: '',
      serviceType: '',
      description: '',
      jobOrder: null,
    }
    updateSelectedEmployeeIds()
  }

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
    loadIncidentData,
    clearForm,
    updateSelectedEmployeeIds,
  }
}