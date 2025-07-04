import type { PageProps } from '@inertiajs/core'
import type { LucideIcon } from 'lucide-vue-next'
import type { Config } from 'ziggy-js'

export interface Auth {
  user: User
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
    employee_id: any;
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
  id: number
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
  truckNo: string
  paymentType: string
  appraisedDate: string
  approvedDate: string
  from: string
  to: string
  teamLeaderId: number
  teamDriverId: number
  safetyOfficerId: number
  teamMechanicId: number
  createdAt: string
  updatedAt: string
  teamLeader: Employee
  teamDriver: Employee
  safetyOfficer: Employee
  teamMechanic: Employee
  haulers: Employee[]
}

export interface EloquentCollection {
  current_page: number
  last_page: number
  per_page: number
  from: number
  to: number
  total: number
}
