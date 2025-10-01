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
      <span class="flex sm:hidden"> Correction </span>
      <span class="hidden sm:flex"> Request Correction </span>
    </Button>
    <Button
      v-else
      variant="outline"
      @click="isEditing = false"
    >
      <X class="sm:mr-2" />
      <span class="flex sm:hidden"> Cancel </span>
      <span class="hidden sm:flex"> Cancel Correction </span>
    </Button>
  </div>
</template>
