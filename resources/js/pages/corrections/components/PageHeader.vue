<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { useJobOrderDicts } from '@/composables/useJobOrderDicts'
import { JobOrderCorrection } from '@/types'
import ChangesModal from './ChangesModal.vue'
import ReasonCard from './ReasonCard.vue'

interface PageHeaderProps {
  correction: JobOrderCorrection
}

const props = defineProps<PageHeaderProps>()

const { correctionStatusMap } = useJobOrderDicts()

const correctionStatus = correctionStatusMap[props.correction.status]
</script>

<template>
  <div>
    <div class="flex flex-row items-center justify-between gap-4 pb-2">
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-4">
          <h3 class="scroll-m-20 text-3xl font-bold text-muted-foreground">
            Correction Request for
            <span class="tracking-tighter text-foreground">
              {{ correction.jobOrder.ticket }}
            </span>
          </h3>
          <Badge
            :variant="correctionStatus?.badge"
            class="overflow-hidden truncate text-ellipsis"
          >
            {{ correctionStatus?.label }}
          </Badge>
        </div>
      </div>
      <ChangesModal
        :changes="correction.properties"
        :status="correction.status"
      />
    </div>
    <ReasonCard :correction="correction" />
  </div>
</template>
