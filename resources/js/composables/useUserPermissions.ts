import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useUserPermissions = () => {
  const user = computed(() => usePage().props.auth.user)

  const isHR = computed(() => {
    return user.value?.employee?.position?.name === 'Human Resource'
  })

  const isTeamLeader = computed(() => {
    return user.value?.employee?.position?.name === 'Team Leader'
  })

  const isSafetyOfficer = computed(() => {
    return user.value?.employee?.position?.name === 'Safety Officer'
  })

  const isConsultant = computed(() => {
    return user.value?.employee?.position?.name === 'Consultant'
  })

  const canVerify = computed(() => isHR.value)
  const canCompose = computed(
    () => isHR.value || isTeamLeader.value || isSafetyOfficer.value,
  )
  const isCreatingRole = computed(
    () => isTeamLeader.value || isSafetyOfficer.value,
  )

  return {
    isHR,
    isTeamLeader,
    isSafetyOfficer,
    isConsultant,
    canVerify,
    canCompose,
    isCreatingRole,
  }
}
