import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
  const authUserPermissions = new Set(
    usePage<SharedData>().props.auth.permissions,
  )

  const can = (permission: string) => {
    return authUserPermissions.has(permission)
  }

  const cannot = (permission: string) => {
    return !authUserPermissions.has(permission)
  }

  return {
    can,
    cannot,
  }
}
