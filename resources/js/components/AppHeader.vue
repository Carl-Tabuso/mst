<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
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
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { getInitials } from '@/composables/useInitials'
import { usePermissions } from '@/composables/usePermissions'
import { SharedData, type BreadcrumbItem, type NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Archive,
  Award,
  ChartPie,
  ClipboardList,
  FilePenLine,
  History,
  Home,
  Menu,
  Pencil,
  UserRoundCog,
  UsersRound,
} from 'lucide-vue-next'
import { computed } from 'vue'
import DarkModeToggle from './DarkModeToggle.vue'

interface Props {
  breadcrumbs?: BreadcrumbItem[]
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
})

const page = usePage<SharedData>()
const auth = computed(() => page.props.auth)

const isCurrentRoute = computed(() => (url: string) => page.url === url)

const activeItemStyles = computed(
  () => (url: string) => (isCurrentRoute.value(url) ? 'text-primary' : ''),
)

const { can, canAny } = usePermissions()

const canAccessJobOrders = computed(() => {
  return canAny({
    roles: ['frontliner', 'head frontliner', 'team leader', 'dispatcher'],
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
    can: canAccessJobOrders.value,
  },
  {
    title: 'Job Order Corrections',
    href: '/job-orders/corrections',
    icon: Pencil,
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
    href: '#',
    icon: UsersRound,
    can: can('manage:employees'),
  },
  {
    title: 'Incident Reports',
    href: '/incidents/report',
    icon: FilePenLine,
    can: can('manage:incident_reports'),
  },
  {
    title: 'Performance Monitoring',
    href: '/performances',
    icon: Award,
    can: can('view:performances'),
  },
  {
    title: 'Reports and Analytics',
    href: '/reports',
    icon: ChartPie,
    can: can('view:reports_analytics'),
  },
  {
    title: 'Archives',
    href: '/archives',
    icon: Archive,
    can: can('update:job_order'),
  },
  {
    title: 'Activity Logs',
    href: '/activities',
    icon: History,
    can: can('view:activity_logs'),
  },
]

const rightNavItems: NavItem[] = [
  // {
  //   title: 'Repository',
  //   href: 'https://github.com/laravel/vue-starter-kit',
  //   icon: Folder,
  // },
  // {
  //   title: 'Documentation',
  //   href: 'https://laravel.com/docs/starter-kits',
  //   icon: BookOpen,
  // },
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
                  <Link
                    v-for="item in mainNavItems"
                    :key="item.title"
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
                </nav>
                <div class="flex flex-col space-y-4">
                  <a
                    v-for="item in rightNavItems"
                    :key="item.title"
                    :href="item.href"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center space-x-2 text-sm font-medium"
                  >
                    <component
                      v-if="item.icon"
                      :is="item.icon"
                      class="h-5 w-5"
                    />
                    <span>{{ item.title }}</span>
                  </a>
                </div>
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
              <NavigationMenuItem
                v-for="(item, index) in mainNavItems"
                :key="index"
                class="relative flex h-full items-center"
              >
                <Link
                  v-if="item?.can"
                  :href="item.href"
                >
                  <NavigationMenuLink
                    :class="[
                      navigationMenuTriggerStyle(),
                      activeItemStyles(item.href),
                    ]"
                  >
                    <!-- <component
                      v-if="item.icon"
                      :is="item.icon"
                      class="mr-2 h-4 w-4"
                    /> -->
                    {{ item.title }}
                  </NavigationMenuLink>
                </Link>
                <div
                  v-if="isCurrentRoute(item.href)"
                  class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-primary"
                ></div>
              </NavigationMenuItem>
            </NavigationMenuList>
          </NavigationMenu>
        </div>

        <div class="ml-auto flex items-center space-x-2">
          <div class="relative flex items-center space-x-1">
            <!-- <Button
              variant="ghost"
              size="icon"
              class="group h-9 w-9 cursor-pointer"
            >
              <Search class="size-5 opacity-80 group-hover:opacity-100" />
            </Button> -->
            <DarkModeToggle />

            <div class="hidden space-x-1 lg:flex">
              <template
                v-for="item in rightNavItems"
                :key="item.title"
              >
                <TooltipProvider :delay-duration="0">
                  <Tooltip>
                    <TooltipTrigger>
                      <Button
                        variant="ghost"
                        size="icon"
                        as-child
                        class="group h-9 w-9 cursor-pointer"
                      >
                        <a
                          :href="item.href"
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          <span class="sr-only">{{ item.title }}</span>
                          <component
                            :is="item.icon"
                            class="size-5 opacity-80 group-hover:opacity-100"
                          />
                        </a>
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>{{ item.title }}</p>
                    </TooltipContent>
                  </Tooltip>
                </TooltipProvider>
              </template>
            </div>
          </div>

          <DropdownMenu>
            <DropdownMenuTrigger :as-child="true">
              <Button
                variant="ghost"
                size="icon"
                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
              >
                <Avatar class="size-8 overflow-hidden rounded-full">
                  <AvatarImage
                    v-if="auth.user.avatar"
                    :src="auth.user.avatar"
                    :alt="auth.user.employee.full_name"
                  />
                  <AvatarFallback
                    class="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                  >
                    {{ getInitials(auth.user.employee.full_name) }}
                  </AvatarFallback>
                </Avatar>
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

    <!-- <div
      v-if="props.breadcrumbs.length > 1"
      class="flex w-full"
    >
      <div
        class="mx-auto flex h-12 w-full items-center justify-start px-6 text-neutral-500 md:max-w-7xl"
      >
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
      </div>
    </div> -->
  </div>
</template>
