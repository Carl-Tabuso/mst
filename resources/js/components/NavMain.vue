<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from '@/components/ui/sidebar'
import { type NavItem, type SharedData } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import { computed } from 'vue'
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from './ui/collapsible'

defineProps<{
  items: NavItem[]
}>()

const page = usePage<SharedData>()

const currentUrl = computed(() => page.url.split('?')[0])
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Menu</SidebarGroupLabel>
    <SidebarMenu>
      <Collapsible
        v-for="item in items"
        :key="item.title"
        as-child
        :default-open="item.isActive"
        class="group/collapsible"
      >
        <SidebarMenuItem>
          <div v-if="item.items?.length">
            <CollapsibleTrigger as-child>
              <SidebarMenuButton
                :tooltip="item.title"
                :is-active="
                  item.items.flatMap((i) => i.href).includes(currentUrl)
                "
              >
                <component
                  :is="item.icon"
                  v-if="item.icon"
                />
                <span>{{ item.title }}</span>
                <ChevronRight
                  class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                />
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubItem
                  v-for="subItem in item.items"
                  :key="subItem.title"
                >
                  <SidebarMenuSubButton
                    as-child
                    :class="{ 'font-semibold': subItem.href === currentUrl }"
                  >
                    <Link
                      :href="subItem.href"
                      preserve-state
                    >
                      <span>{{ subItem.title }}</span>
                    </Link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </div>
          <div v-else>
            <SidebarMenuButton
              as-child
              :is-active="item.href === currentUrl"
              :tooltip="item.title"
            >
              <Link
                :href="item.href"
                preserve-state
              >
                <component :is="item.icon" />
                <span>{{ item.title }}</span>
              </Link>
            </SidebarMenuButton>
          </div>
        </SidebarMenuItem>
      </Collapsible>
    </SidebarMenu>
  </SidebarGroup>
</template>
