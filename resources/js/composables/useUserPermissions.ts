import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useUserPermissions = () => {
  const user = computed(() => usePage().props.auth.user)

  const isHR = computed(() => {
    return user.value?.roles?.[0]?.name === 'human resource'
  })

  const isTeamLeader = computed(() => {
    return user.value?.roles?.[0]?.name === 'team leader'
  })

  const isSafetyOfficer = computed(() => {
    return user.value?.roles?.[0]?.name === 'safety officer'
  })

  const isConsultant = computed(() => {
    return user.value?.roles?.[0]?.name === 'consultant'
  })

  const isHeadFrontliner = computed(() => {
    return user.value?.roles?.[0]?.name === 'head frontliner'
  })

  const isFrontliner = computed(() => {
    return user.value?.roles?.[0]?.name === 'frontliner'
  })

  const isDispatcher = computed(() => {
    return user.value?.roles?.[0]?.name === 'dispatcher'
  })

  const isITAdmin = computed(() => {
    return user.value?.roles?.[0]?.name === 'it admin'
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
    isHeadFrontliner,
    isFrontliner,
    isDispatcher,
    isITAdmin,
    canVerify,
    canCompose,
    isCreatingRole,
  }
}
