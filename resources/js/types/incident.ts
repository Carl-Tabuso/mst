export interface Employee {
  id: number
  name: string
  email?: string
}

export interface JobOrder {
  id: number
  serviceable_type: string
  status: string
  label?: string
creator?: string
}

export interface Mail {
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
   job_order?: JobOrder
  labels?: string[]
}

export interface MailProps {
  defaultLayout?: number[]
  defaultCollapsed?: boolean
  navCollapsedSize: number
  isComposing?: boolean
}

export interface MailListProps {
  initialItems?: Mail[]
  searchQuery?: string
  activeTab?: string
  selectedStatuses?: string[]
  dateFrom?: string
  dateTo?: string
  sortBy?: string
}

export interface MailFilters {
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
  serviceType: string
  description: string
  jobOrder: number | null
}

export interface MailDisplayProps {
  mail: Mail | undefined
  isComposing?: boolean
}

export interface JobOrderOption {
  id: number
  label: string
  service_type: string
}