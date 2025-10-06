<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { format } from 'date-fns'
import { CheckCircle, Clock, XCircle } from 'lucide-vue-next'
import { JobOrderCorrectionRequestStatus } from '..'

interface JobOrderCorrectionRequestsProps {
  data?: JobOrderCorrectionRequestStatus[]
}

defineProps<JobOrderCorrectionRequestsProps>()

const statBindings = {
  Pending: {
    icon: Clock,
    bgClass: 'bg-warning',
    textClass: 'text-warning',
  },
  Approved: {
    icon: CheckCircle,
    bgClass: 'bg-tertiary/75',
    textClass: 'text-tertiary',
  },
  Rejected: {
    icon: XCircle,
    bgClass: 'bg-destructive/75',
    textClass: 'text-destructive',
  },
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex items-center gap-2">
      <div class="text-sm font-medium">
        Correction Requests
        <span class="pl-2 text-xs font-semibold text-muted-foreground">
          {{ format(new Date(), 'y') }}
        </span>
      </div>
      <Separator class="flex-1" />
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <Card
        v-for="(correction, index) in data"
        :key="index"
        class="group shadow transition-all hover:translate-y-[-2px] hover:shadow-lg"
      >
        <CardContent class="flex flex-row items-center gap-4 p-5">
          <div
            :class="[
              'flex h-12 w-12 items-center justify-center rounded-full text-white',
              statBindings[correction.status].bgClass,
            ]"
          >
            <component
              :is="statBindings[correction.status].icon"
              class="size-6"
            />
          </div>
          <div>
            <div>
              <p class="text-sm font-medium text-muted-foreground">
                {{ correction.status }}
              </p>
              <p
                :class="[
                  'text-2xl font-bold',
                  statBindings[correction.status].textClass,
                ]"
              >
                {{ correction.total }}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
