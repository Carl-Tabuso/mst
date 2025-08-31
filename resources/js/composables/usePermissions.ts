import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
  const page = usePage<SharedData>()

  const authUserPermissions = new Set(page.props.auth.permissions)

  const authUserRole = page.props.auth.user.roles[0].name

  const can = (permission: string) => {
    return authUserPermissions.has(permission)
  }

  const cannot = (permission: string) => {
    return !authUserPermissions.has(permission)
  }

  const canAny = ({
    permissions,
    roles,
  }: {
    permissions?: string[]
    roles?: string[]
  }) => {
    const authorizedRoles = roles?.includes(authUserRole)
    const authorizedPermissions = [...authUserPermissions].some((permission) =>
      permissions?.includes(permission),
    )

    return authorizedRoles || authorizedPermissions
  }

  const hasRole = (role: string) => {
    return authUserRole === role
  }

  return {
    can,
    cannot,
    canAny,
    hasRole,
  }
}
