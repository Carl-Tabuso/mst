<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import {
  Archive,
  Award,
  BookOpen,
  ChartPie,
  ClipboardList,
  FilePenLine,
  History,
  Home,
  UserRoundCog,
  UsersRound,
} from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'Job Order',
        href: '#',
        icon: ClipboardList,
        items: [
            {
                title: 'Lists',
                href: '/job-orders',
            },
            {
                title: 'Corrections',
                href: '/job-orders/corrections',
            }
        ],
    },
    {
        title: 'User Management',
        href: '/users',
        icon: UserRoundCog,
    },
    {
        title: 'Incident Report',
        href: '/incidents/report',
        icon: FilePenLine,
    },
    {
        title: 'Performance Monitoring',
        href: '/ratings/',
        icon: Award,
    },
    {
        title: 'Reports and Analytics',
        href: '#',
        icon: ChartPie
    },
    {
        title: 'Archives',
        href: '/archives',
        icon: Archive
    }
];

const footerNavItems: NavItem[] = [
  {
    title: 'Roles and Permissions',
    href: '#',
    icon: BookOpen,
  },
]
</script>

<template>
  <Sidebar
    collapsible="icon"
    variant="sidebar"
  >
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton
            size="lg"
            as-child
          >
            <Link :href="route('home')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
