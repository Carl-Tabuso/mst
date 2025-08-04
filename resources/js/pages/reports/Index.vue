<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

interface IndexProps {
  //
}

defineProps<IndexProps>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Reports and Analytics',
    href: route('activity.index'),
  },
]
</script>

<template>
  <Head title="Reports and Analytics" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-6">
      <div class="flex items-center justify-between">
        <div class="flex flex-col">
          <h1 class="scroll-m-20 text-3xl font-bold text-primary">
            Reports and Analytics
          </h1>
          <p class="text-muted-foreground">
            Track performance, trends, and key metrics across the system
          </p>
        </div>
        <div>
          <!--Export and year filter-->
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline">
                Select Year
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              Hello
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
