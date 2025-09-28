export interface Employee {
  id: number
  name: string
  email?: string
}

export interface JobOrder {
  id: number
  serviceable_type: string
  service_type: string
  status: string
  label?: string
  creator?: string
}

export interface Incident {
  id: string
  subject: string
  description: string
  plainText: string
  html: string
  is_read?: boolean
  occured_at: string
  location: string
  infraction_type: string
  status: string
  created_by: Employee
  involved_employees: Employee[]
  haulers: Employee[]
  hauling_job_order?: JobOrder
  labels?: string[]
  hauling?: any
}

export interface IncidentProps {
  defaultLayout?: number[]
  defaultCollapsed?: boolean
  navCollapsedSize: number
  isEditing?: boolean
}

export interface IncidentListProps {
  initialItems?: Incident[]
  searchQuery?: string
  activeTab?: string
  selectedStatuses?: string[]
  dateFrom?: string
  dateTo?: string
  sortBy?: string
}

export interface IncidentFilters {
  statuses: string[]
  dateFrom: string
  dateTo: string
}

export interface EmployeeSelection {
  id: number
  name: string
}

export interface FormData {
  subject: string
  involvedEmployees: EmployeeSelection[]
  dateTime: string
  location: string
  infractionType: string
  description: string
  jobOrder: number | null
  jobOrderTicket?: ''
}

export interface IncidentDisplayProps {
  incident: Incident | undefined
  isEditing?: boolean
}

export interface JobOrderOption {
  id: number
  label: string
  service_type: string
}
