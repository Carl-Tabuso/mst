export interface JobOrder {
  id: number
  status?: string
  client?: string
  serviceable_type?: string
  created_at?: string
  updated_at?: string
}

export interface ITService {
  id: number
  machine_type: string
  model: string
}

export interface ProfileProps {
  employee: {
    full_name: string
    id: number
    first_name: string
    middle_name: string
    last_name: string
    suffix: string
    position: { name: string } | null
    itServicesAsTechnician: ITService[]
    createdJobOrders: JobOrder[]
  }
  assignedJobOrders: JobOrder[]
  averagePerformanceRating: number | null
  jobOrderStats: {
    total: number
    by_status: Record<string, number>
  } | null

  createdJobOrders?: {
    total: number
    by_status: Record<string, number>
  } | null

  createdJobOrdersList?: JobOrder[]
}
