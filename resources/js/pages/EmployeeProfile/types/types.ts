export interface JobOrder {
  id: number | string
  ticket?: string
  status?: string
  client?: string
  serviceable_type?: string
  team_leader?: string
  safety_officer?: string
  service_area?: string
  created_at?: string
  updated_at?: string
}

export interface ITService {
  id: number
  machine_type: string
  model: string
}

export interface PerformanceStats {
  total_created: number
  monthly_created: number
  completed_jobs: number
  corrections_count?: number
  success_rate: number
  current_month: string
  error_count?: number
}

export interface TeamStats {
  teams_managed: number
  total_team_job_orders: number
  team_success_rate: number
}

export interface Employee {
  id: number
  full_name: string
  first_name: string
  middle_name: string | null
  last_name: string
  suffix: string | null
  email?: string
  avatar_url?: string | null
  position: { name: string } | null
  itServicesAsTechnician?: ITService[]
}

export interface EnhancedProfileProps {
  employee: Employee
  assignedJobOrders: JobOrder[]
  averagePerformanceRating: number | null
  jobOrderStats: {
    total: number
    by_status: Record<string, number>
  } | null
  position: string

  createdJobOrders?: {
    total: number
    by_status: Record<string, number>
  } | null
  createdJobOrdersList?: JobOrder[] | null
  performanceStats?: PerformanceStats | null
  performanceEvaluations?: any[] | null
  teamStats?: TeamStats | null
  itServices?: ITService[] | null
}

export interface ProfileProps {
  employee: Employee
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