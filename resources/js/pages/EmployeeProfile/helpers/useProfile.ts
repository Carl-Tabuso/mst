import { computed } from 'vue'
import { EnhancedProfileProps } from '../types/types'

export const useProfile = (props: EnhancedProfileProps) => {
  const positionName = computed(
    () => props.position || props.employee.position?.name?.toLowerCase() || '',
  )

  const showJobOrders = computed(() =>
    ['team leader', 'hauler', 'driver', 'safety officer'].includes(positionName.value),
  )

  const showITServices = computed(() =>
    ['technician'].includes(positionName.value),
  )

  const showPerformance = computed(() =>
    [
      'frontliner',
      'head frontliner',
    ].includes(positionName.value),
  )

  const showTeamLeaderStats = computed(() =>
    positionName.value === 'team leader'
  )

  const showFrontlinerContent = computed(() =>
    positionName.value === 'frontliner' || 'head frontliner'
  )

  // Show job orders that were created (for frontliners and admins)
  const showJobOrdersCreated = computed(() =>
    ['frontliner', 'head frontliner', 'admin'].includes(positionName.value),
  )

  const formatStatus = (status: string) =>
    status.replace(/[_-]/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())

  const getStatusColor = (status: string) => {
    const lower = status.toLowerCase()
    if (lower.includes('pending') || lower.includes('waiting'))
      return 'text-yellow-600 bg-yellow-100'
    if (lower.includes('progress') || lower.includes('processing'))
      return 'text-blue-600 bg-blue-100'
    if (lower.includes('completed') || lower.includes('done'))
      return 'text-green-600 bg-green-100'
    if (lower.includes('failed') || lower.includes('rejected'))
      return 'text-red-600 bg-red-100'
    return 'text-gray-600 bg-gray-100'
  }

  return {
    positionName,
    showJobOrders,
    showITServices,
    showPerformance,
    showTeamLeaderStats,
    showFrontlinerContent,
    showJobOrdersCreated,
    formatStatus,
    getStatusColor,
  }
}