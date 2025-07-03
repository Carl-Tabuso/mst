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

  const canVerify = computed(() => isHR.value)
  const canCompose = computed(() => isHR.value || isTeamLeader.value)

  return {
    isHR,
    isTeamLeader,
    canVerify,
    canCompose,
  }
}
