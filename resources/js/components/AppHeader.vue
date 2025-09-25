<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu'
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from '@/components/ui/sheet'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { usePermissions } from '@/composables/usePermissions'
import { UserRoleType } from '@/constants/user-role'
import { SharedData, type BreadcrumbItem, type NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Archive,
  Award,
  ChartPie,
  ClipboardList,
  ClipboardPen,
  FilePenLine,
  History,
  Home,
  Menu,
  Star,
  Truck,
  UserRoundCog,
  UsersRound,
} from 'lucide-vue-next'
import { computed } from 'vue'
import DarkModeToggle from './DarkModeToggle.vue'
import UserAvatar from './UserAvatar.vue'
import Breadcrumbs from './Breadcrumbs.vue'

interface Props {
  breadcrumbs?: BreadcrumbItem[]
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
})

const page = usePage<SharedData>()
const auth = computed(() => page.props.auth)

const isCurrentRoute = computed(() => {
  return (url: string) => page.url.split('?')[0] === url
})

const activeItemStyles = computed(
  () => (url: string) => (isCurrentRoute.value(url) ? 'text-primary' : ''),
)

const { can, canAny } = usePermissions()

const archiveRoutes: Partial<Record<UserRoleType, string>> = {
  frontliner: 'archives/job-orders',
  'human resource': 'archives/employees',
  'it admin': 'archives/users',
}

const archiveUrl = computed(() => {
  return (
    Object.entries(archiveRoutes).find((role) => {
      return role[0] === auth.value.user.roles[0].name
    })?.[1] ?? '#'
  )
})

const canAccessPerformanceMonitoring = computed(() => {
  return can('view:performances')
})

const canAccessPerformanceRating = computed(() => {
  return canAny({
    roles: ['team leader', 'regular'],
  })
})

const mainNavItems: NavItem[] = [
  {
    title: 'Home',
    href: '/',
    icon: Home,
    can: true,
  },
  {
    title: 'Job Order List',
    href: '/job-orders',
    icon: ClipboardList,
    can: canAny({
      roles: ['frontliner', 'head frontliner', 'team leader', 'dispatcher'],
    }),
  },
  {
    title: 'Job Order Corrections',
    href: '/job-orders/corrections',
    icon: ClipboardPen,
    can: canAny({ roles: ['frontliner', 'head frontliner'] }),
  },
  {
    title: 'User Management',
    href: '/users',
    icon: UserRoundCog,
    can: can('manage:users'),
  },
  {
    title: 'Employee Management',
    href: '/employee-management',
    icon: UsersRound,
    can: can('manage:employees'),
  },
  {
    title: 'Incident Reports',
    href: '/incidents',
    icon: FilePenLine,
    can: can('manage:incident_reports'),
  },
  {
    title: 'Performance Monitoring',
    href: '/ratings/table',
    icon: Award,
    can: canAccessPerformanceMonitoring.value,
  },
  {
    title: 'Performance Rating',
    href: '/ratings',
    icon: Star,
    can: canAccessPerformanceRating.value,
  },
  {
    title: 'Reports and Analytics',
    href: '/reports',
    icon: ChartPie,
    can: can('view:reports_analytics'),
  },
  {
    title: 'Archives',
    href: archiveUrl.value,
    can: archiveUrl.value !== '#',
    icon: Archive,
  },
  {
    title: 'Truck Inventory',
    href: '/trucks',
    icon: Truck,
    can: can('assign:hauling_personnel'),
  },
  {
    title: 'Activity Logs',
    href: '/activities',
    icon: History,
    can: true,
  },
]
</script>

<template>
  <div class="sticky top-0 z-10 bg-background">
    <div class="border-b border-sidebar-border/80">
      <div class="mx-auto flex h-14 items-center px-6 md:max-w-7xl">
        <!-- Mobile Menu -->
        <div class="lg:hidden">
          <Sheet>
            <SheetTrigger :as-child="true">
              <Button
                variant="ghost"
                size="icon"
                class="mr-2 h-9 w-9"
              >
                <Menu class="h-5 w-5" />
              </Button>
            </SheetTrigger>
            <SheetContent
              side="left"
              class="w-[300px] p-6"
            >
              <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
              <SheetHeader class="flex justify-start text-left">
                <AppLogoIcon
                  class="size-6 fill-current text-black dark:text-white"
                />
              </SheetHeader>
              <div
                class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
              >
                <nav class="-mx-3 space-y-1">
                  <template
                    v-for="item in mainNavItems"
                    :key="item.title"
                  >
                    <Link
                      v-if="item.can"
                      :href="item.href"
                      class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                      :class="activeItemStyles(item.href)"
                    >
                      <component
                        v-if="item.icon"
                        :is="item.icon"
                        class="h-5 w-5"
                      />
                      {{ item.title }}
                    </Link>
                  </template>
                </nav>
              </div>
            </SheetContent>
          </Sheet>
        </div>

        <Link
          :href="route('home')"
          class="flex items-center gap-x-2"
        >
          <AppLogo />
        </Link>

        <!-- Desktop Menu -->
        <div class="hidden h-full lg:flex lg:flex-1">
          <NavigationMenu class="ml-10 flex h-full items-stretch">
            <NavigationMenuList class="flex h-full items-stretch">
              <template
                v-for="(item, index) in mainNavItems"
                :key="index"
              >
                <NavigationMenuItem
                  v-if="item?.can"
                  class="relative flex h-full items-center"
                >
                  <Link :href="item.href">
                    <NavigationMenuLink
                      :class="[
                        navigationMenuTriggerStyle(),
                        activeItemStyles(item.href),
                      ]"
                    >
                      {{ item.title }}
                    </NavigationMenuLink>
                  </Link>
                  <div
                    v-if="isCurrentRoute(item.href)"
                    class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-primary"
                  />
                </NavigationMenuItem>
              </template>
            </NavigationMenuList>
          </NavigationMenu>
        </div>

        <div class="ml-auto flex items-center space-x-2">
          <div class="relative flex items-center space-x-1">
            <DarkModeToggle />
          </div>

          <DropdownMenu>
            <DropdownMenuTrigger :as-child="true">
              <Button
                variant="ghost"
                size="icon"
                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
              >
                <UserAvatar
                  :avatar-path="auth.user.avatar"
                  :fallback="auth.user.employee.full_name"
                  class="size-8"
                />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent
              align="end"
              class="w-56"
            >
              <UserMenuContent :user="auth.user" />
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </div>
    <div
      v-if="breadcrumbs.length > 1"
      class="flex w-full"
    >
      <div
        class="mx-auto flex h-12 w-full items-center justify-start px-6 text-muted-foreground md:max-w-7xl"
      >
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </div>
    </div>
    <div v-else class="mb-3" />
  </div>
</template>
