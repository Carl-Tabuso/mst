<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Pencil, Search } from 'lucide-vue-next'
import { ref } from 'vue'
import AddNewTruck from './components/AddNewTruck.vue'
import { Truck } from '@/types'
import { useDebounce, useDebounceFn } from '@vueuse/core'
import { router } from '@inertiajs/vue3'

interface IndexProps {
  data: Truck[]
}

const props = defineProps<IndexProps>()

const search = ref<string>('')

const onSearchInput = (query: string) => {
  search.value = query
  searchDebounceFn()
}

const searchDebounceFn = useDebounceFn(() => {
  router.get(route('truck.index'), {
    search: search.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 500)
</script>

<template>
  <Head title="Truck Inventory" />
  <AppLayout>
    <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
      <div class="flex flex-row items-center justify-between">
        <div class="flex flex-col">
          <h3 class="scroll-m-20 text-3xl font-bold text-primary">
            Truck Inventory
          </h3>
          <p class="text-muted-foreground">
            You can manage the trucks being used for waste management haulings here.
          </p>
        </div>
        <AddNewTruck />
      </div>

      <div class="mt-6">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
          <div class="flex items-center py-1 gap-1 lg:flex-row justify-between">
            <div class="flex-1 flex flex-row items-center gap-1">
              <div class="relative w-full max-w-md items-center">
                <Input
                  id="search"
                  type="text"
                  class="h-9 max-w-lg pl-12"
                  placeholder="Search model or plate number"
                  :model-value="search"
                  @update:model-value="onSearchInput"
                />
                <span
                  class="absolute inset-y-0 start-0 flex items-center justify-center px-5"
                >
                  <Search class="size-4 text-muted-foreground" />
                </span>
              </div>
              <Button variant="ghost">
                <Download class="mr-2" />
                Export All
              </Button>              
            </div>
            <span class="flex flex-row items-center gap-1">
              <p class="text-sm">Total</p>
              <Badge variant="secondary" class="rounded-full font-bold">
                {{ props.data.data.length }}
              </Badge>
            </span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 sm:grid-cols-2 gap-4">
            <div
              v-for="truck in props.data.data"
              :key="truck.id"
              class="flex items-center justify-between rounded-md border px-4 py-3 bg-muted/50 hover:bg-muted/20 cursor-pointer"
              v-on:click="console.log(truck.model)"
            >
              <div class="flex items-center gap-4">
                <div class="flex flex-col">
                  <span class="flex items-center gap-3">
                    {{ truck.plateNo }}
                    <span class="text-muted-foreground text-[13px] font-semibold">
                      {{ truck.model }}
                    </span>
                  </span>
                  <span class="text-xs text-muted-foreground">
                    Added by {{ truck.creator?.fullName }} last Monday
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
