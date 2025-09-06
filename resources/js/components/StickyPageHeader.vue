<script setup lang="ts">
import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'

const page = usePage<SharedData>()

const isNotHeadFrontliner =
  page.props.auth.user.roles[0].name !== 'head frontliner'
</script>

<template>
  <div
    class="sticky top-0 z-10 border-b border-border bg-background shadow-sm"
    :class="{ 'top-[57px]': isNotHeadFrontliner }"
  >
    <slot />
  </div>
</template>
