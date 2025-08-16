<script setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Activity } from 'lucide-vue-next'
</script>

<template>
  <Card class="shadow">
    <CardHeader class="py-4">
      <CardTitle
        class="text-md text-center font-semibold text-lime-600 dark:text-lime-600"
      >
        Check Vital Signs
      </CardTitle>
    </CardHeader>
    <CardContent>
      <div
        class="flex h-[170px] w-[170px] items-center justify-center rounded-full border bg-lime-50 transition-transform duration-200 hover:scale-105 dark:bg-lime-900"
      >
        <div
          class="flex h-[140px] w-[140px] items-center justify-center rounded-full border bg-lime-200 dark:bg-lime-800"
        >
          <a
            href="/pulse"
            target="_blank"
          >
            <div
              class="flex h-[110px] w-[110px] items-center justify-center rounded-full border bg-lime-400 text-white dark:bg-lime-600"
            >
              <Activity class="h-[58px] w-[58px]" />
            </div>
          </a>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
