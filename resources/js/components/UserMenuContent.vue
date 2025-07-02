<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue'
import {
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu'
import type { User } from '@/types'
import { Link } from '@inertiajs/vue3'
import { LogOut, Settings } from 'lucide-vue-next'

interface Props {
  user: User
}

defineProps<Props>()
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="`/profile/${user.employee_id}`" as="button">
                <span class="mr-2 h-4 w-4 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                Profile
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="route('profile.edit')" as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
      <Link
        class="block w-full"
        :href="route('profile.edit')"
        as="button"
      >
        <Settings class="mr-2 h-4 w-4" />
        Settings
      </Link>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator />
  <DropdownMenuItem :as-child="true">
    <Link
      class="block w-full"
      method="post"
      :href="route('logout')"
      as="button"
    >
      <LogOut class="mr-2 h-4 w-4" />
      Log out
    </Link>
  </DropdownMenuItem>
</template>
