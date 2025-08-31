import { CorrectionStatusType } from '@/constants/correction-statuses'
import { HaulingStatusType } from '@/constants/hauling-statuses'
import { JobOrderStatus } from '@/constants/job-order-statuses'
import { UserRoleType } from '@/constants/user-role'
import type { PageProps } from '@inertiajs/core'
import type { LucideIcon } from 'lucide-vue-next'
import type { Config } from 'ziggy-js'

export interface Auth {
  user: AuthUser
  permissions: string[]
}

export interface AuthUser {
  employee_id: number
  avatar: string
  email: string
  employee: {
    first_name: string
    middle_name: string | null
    last_name: string
    full_name: string
  }
  roles: {
    name: UserRoleType
    permissions: {
      name: string
    }[]
  }[]
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
  can?: boolean
}

export interface SharedData extends PageProps {
  name: string
  quote: { message: string; author: string }
  auth: Auth
  ziggy: Config & { location: string }
  flash: { message: any }
}

export interface User {
  employee_id: any
  id: number
  email: string
  avatar?: string
  emailVerifiedAt?: string
  createdAt: string
  updatedAt: string
  deletedAt?: string
  employee: Employee
  roles: { id: number; name: UserRoleType }[]
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
  status: JobOrderStatus
  errorCount: number
  createdAt: string
  updatedAt: string
  creator: Employee
  serviceable: Form4 // add it services and others here
  cancel: CancelledJobOrder
  corrections: JobOrderCorrection[]
}

export interface Form4 {
  id: number
  paymentDate: string | null
  bidBond: string | null
  orNumber: string | null
  createdAt: string
  updatedAt: string
  jobOrder: JobOrder
  appraisers: Employee[]
  form3: Form3
  dispatcher: Employee | null
}

export interface Form3 {
  id: number
  form4Id: number
  paymentType: string | null
  appraisedDate: string | null
  approvedDate: string | null
  from: string | null
  to: string | null
  createdAt: string
  updatedAt: string
  form4: Form4
  haulings: Form3Hauling[]
}

export interface Form3Hauling {
  id: number
  form3Id: number
  date: string
  form3: Form3
  assignedPersonnel: Form3AssignedPersonnel
  haulers: Employee[]
  checklist: Form3HaulingChecklist
  truck: Truck
  status: HaulingStatusType
  isOpen: boolean
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
  createdAt: string
  updatedAt: string
  hauling: Form3Hauling
  teamLeader: Employee | null
  teamDriver: Employee | null
  safetyOfficer: Employee | null
  teamMechanic: Employee | null
}

export interface CancelledJobOrder {
  id: number
  jobOrderId: number
  reason: string
  createdAt: string
  updatedAt: string
}

export interface JobOrderCorrection {
  id: number
  jobOrderId: number
  status: CorrectionStatusType
  properties: { before: {}; after: {} }
  reason: string
  approvedAt: string
  createdAt: string
  updatedAt: string
  jobOrder: JobOrder
}

export interface ActivityLog {
  date: string
  items: {
    id: number
    time: string
    log: string
    description: string
    ipAddress: string
    browser: string
    platform: string
    causer?: User
  }[]
}

export interface EloquentCollection {
  current_page: number
  last_page: number
  per_page: number
  from: number
  to: number
  total: number
  links: MetaPaginationLinks[]
}

export interface MetaPaginationLinks {
  url: string | null
  label: string
  active: boolean
  length: number
}

export interface PaginationLinks {
  first: string
  last: string
  next: string | null
  prev: string | null
}

export interface Truck {
  id: number
  model: string
  plateNo: string
  creator?: Employee | null
  createdAt: string
  updatedAt: string
}

export type ServiceType = 'Waste Management' | 'IT Service' | 'Other Services'
