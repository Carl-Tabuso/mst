<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Link } from '@inertiajs/vue3'
import { Clock } from 'lucide-vue-next'
import { AwaitingCorrectionReviewsCard } from '..'

interface AwaitingCorrectionReviewsProps {
  data?: AwaitingCorrectionReviewsCard[]
}

defineProps<AwaitingCorrectionReviewsProps>()
</script>

<template>
  <Card class="shadow transition-shadow hover:shadow-md">
    <CardHeader class="py-4">
      <div>
        <CardTitle class="text-lg font-semibold text-primary">
          Awaiting Correction Reviews
        </CardTitle>
        <CardDescription class="text-sm leading-3 text-muted-foreground">
          Most recent ticket correction requests
        </CardDescription>
      </div>
    </CardHeader>
    <Separator />
    <CardContent class="my-2 h-[375px] divide-y divide-border overflow-y-auto">
      <div
        v-for="correction in data"
        :key="correction.ticket"
        class="flex flex-col gap-1 px-1 py-3"
      >
        <div class="flex items-center justify-between">
          <Link
            :href="route('job_order.correction.show', correction.id)"
            class="text-sm font-medium tracking-tighter text-primary hover:underline"
          >
            {{ correction.ticket }}
          </Link>
          <Badge
            variant="outline"
            class="text-xs font-semibold"
          >
            {{ correction.changesCount }} changes
          </Badge>
        </div>
        <div class="flex justify-between gap-10 text-xs text-muted-foreground">
          <span class="flex items-center gap-2 whitespace-nowrap">
            <Clock class="size-3" /> Requested in
            {{ correction.requestedAt }}
          </span>
          <span class="truncate font-medium">{{ correction.requestedBy }}</span>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
