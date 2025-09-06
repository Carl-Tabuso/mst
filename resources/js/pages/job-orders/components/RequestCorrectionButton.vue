<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Pencil, X } from 'lucide-vue-next'

const isEditing = defineModel<boolean>('isEditing')
</script>

<template>
  <div>
    <Button
      v-if="!isEditing"
      variant="outline"
      @click="isEditing = true"
    >
      <Pencil class="mr-2" />
      Request Correction
    </Button>
    <Button
      v-else
      variant="outline"
      @click="isEditing = false"
    >
      <X class="mr-2" />
      Cancel Correction
    </Button>
  </div>
</template>
