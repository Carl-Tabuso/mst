<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Plus } from 'lucide-vue-next'
import { VisuallyHidden } from 'radix-vue'
</script>

<template>
  <Dialog>
    <DialogTrigger as-child>
      <Button>
        <Plus />
        Add New Truck
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle> Add New Truck </DialogTitle>
        <VisuallyHidden>
          <DialogDescription>
            <!---->
          </DialogDescription>
        </VisuallyHidden>
        Some description
      </DialogHeader>
    </DialogContent>
  </Dialog>
</template>
