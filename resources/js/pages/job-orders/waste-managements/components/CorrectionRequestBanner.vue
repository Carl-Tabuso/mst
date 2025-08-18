<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert'
import { JobOrder, JobOrderCorrection } from '@/types';
import { format } from 'date-fns'

interface CorrectionRequestBannerProps {
    correction?: JobOrderCorrection
}

defineProps<CorrectionRequestBannerProps>()
</script>

<template>
    <div
        v-if="correction"
        class="mb-3 flex justify-center"
    >
        <Alert
        variant="warning"
        class="w-full py-1"
        >
        <AlertDescription class="flex items-center justify-center">
            <span class="font-normal">
            This ticket has a pending correction request submitted on
            {{
                format(
                correction.createdAt,
                "MMMM d, yyyy 'at' h:mm a",
                )
            }}.
            </span>
        </AlertDescription>
        </Alert>
    </div>
</template>
