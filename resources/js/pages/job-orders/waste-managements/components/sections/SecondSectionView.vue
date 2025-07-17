<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Label } from '@/components/ui/label'
import { formatToDateString } from '@/composables/useDateFormatter'
import { getInitials } from '@/composables/useInitials'
import { Employee } from '@/types'

interface SecondSectionViewProps {
  appraisers: Employee[]
  appraisedDate: any
}

defineProps<SecondSectionViewProps>()
</script>

<template>
  <div class="mx-7 grid grid-cols-2 px-7 text-sm">
    <div class="flex flex-col gap-y-3">
      <Label class="text-sm font-medium text-muted-foreground"
        >Appraisers</Label
      >
      <div class="flex h-32 w-fit flex-col gap-2 overflow-y-auto pl-2 pr-5">
        <template v-if="appraisers?.length">
          <div
            v-for="appraiser in appraisers"
            :key="appraiser.id"
            class="flex items-center justify-between gap-2 rounded-md text-sm"
          >
            <div class="flex items-center gap-2 overflow-hidden">
              <Avatar class="h-8 w-8 shrink-0 rounded-full">
                <AvatarImage
                  v-if="appraiser?.account?.avatar"
                  :src="appraiser.account.avatar"
                  :alt="appraiser.fullName"
                />
                <AvatarFallback>
                  {{ getInitials(appraiser.fullName) }}
                </AvatarFallback>
              </Avatar>
              <span>{{ appraiser.fullName }}</span>
            </div>
          </div>
        </template>
        <template v-else>
          <span class="text-muted-foreground">No appraisers</span>
        </template>
      </div>
    </div>
    <div class="flex flex-col gap-y-1">
      <Label class="text-sm font-medium text-muted-foreground"
        >Date Appraised</Label
      >
      <div>
        <span v-if="appraisedDate">
          {{ formatToDateString(appraisedDate.toString()) }}
        </span>
        <span
          v-else
          class="text-muted-foreground"
          >Not set</span
        >
      </div>
    </div>
  </div>
</template>
