import type { PageProps } from '@inertiajs/core'
import type { LucideIcon } from 'lucide-vue-next'
import type { Config } from 'ziggy-js'

export interface Auth {
  user: User
  permissions: string[]
}

export interface BreadcrumbItem {
  title: string
  href: string
}

export interface NavItem {
  title: string
  href: string
  icon?: LucideIcon
  isActive?: boolean
  items?: {
    title: string
    href: string
  }[]
}

export interface SharedData extends PageProps {
  name: string
  quote: { message: string; author: string }
  auth: Auth
  ziggy: Config & { location: string }
}

export interface User {
  id: number
  email: string
  avatar?: string
  emailVerifiedAt?: string
  createdAt: string
  updatedAt: string
  deletedAt?: string
  employee: Employee
}

export interface Employee {
  id: number
  firstName: string
  middleName?: string
  lastName: string
  suffix?: string
  fullName: string
  positionId: number
  createdAt: string
  updatedAt: string
  account?: User
}

export interface JobOrder {
  ticket: string
  serviceableId: number
  serviceableType: string
  dateTime: string
  client: string
  address: string
  department: string
  contactNo: string
  contactPerson: string
  contactPosition: string
  createdBy: number
  status: string
  errorCount: number
  createdAt: string
  updatedAt: string
  creator?: Employee
  serviceable?: any // add it services and others here
}

export interface Form4 {
  id: number
  paymentDate: string
  bidBond: string
  orNumber: string
  createdAt: string
  updatedAt: string
  jobOrder: JobOrder
  appraisers: Employee[]
  form3: Form3
}

export interface Form3 {
  id: number
  form4Id: number
  paymentType: string
  appraisedDate: string
  approvedDate: string
  from: string
  to: string
  createdAt: string
  updatedAt: string
  form4: Form4
  haulings: Form3Hauling[]
}

export interface Form3Hauling {
  id: number
  form3Id: number
  truckNo: string
  date: string
  form3: Form3
  assignedPersonnel: Form3AssignedPersonnel
  haulers: Employee[]
  checklist: Form3HaulingChecklist
}

export interface Form3HaulingChecklist {
  id: number
  form3HaulingId: number
  isVehicleInspectionFilled: boolean
  isUniformPpeFilled: boolean
  isToolsEquipmentFilled: boolean
  isCertified: boolean
  createdAt: string
  updatedAt: string
}

export interface Form3AssignedPersonnel {
  id: number
  form3HaulingId: number
  teamLeaderId: number
  teamDriverId: number
  safetyOfficerId: number
  teamMechanicId: number
  createdAt: string
  updatedAt: string
  hauling: Form3Hauling
  teamLeader: Employee
  teamDriver: Employee
  safetyOfficer: Employee
  teamMechanic: Employee
}

export interface EloquentCollection {
  current_page: number
  last_page: number
  per_page: number
  from: number
  to: number
  total: number
}
