<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { UserRoundMinus, UserRoundX, UsersRound } from 'lucide-vue-next'
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">Employee Metrics</div>
      <Separator class="flex-1" />
    </div>

    <div class="grid grid-cols-3 gap-4">
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <UsersRound
            class="size-14 stroke-1 text-primary will-change-transform"
          />
          <div>
            <div class="text-sm font-semibold">Active Status</div>
            <div class="text-xl font-bold text-primary">56</div>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <UserRoundMinus
            class="size-14 stroke-1 text-primary will-change-transform"
          />
          <div>
            <div class="text-sm font-semibold">Inactive Status</div>
            <div class="text-xl font-bold text-primary">137</div>
          </div>
        </CardContent>
      </Card>
      <Card class="shadow">
        <CardContent class="flex items-center gap-4 rounded-lg p-4">
          <UserRoundX
            class="size-14 stroke-1 text-primary will-change-transform"
          />
          <div>
            <div class="text-sm font-semibold">Without Accounts</div>
            <div class="text-xl font-bold text-primary">146</div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
