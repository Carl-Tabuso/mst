<script setup lang="ts">
import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'

const page = usePage<SharedData>()
const isFrontliner = page.props.auth.user.roles[0].name === 'head frontliner'
</script>

<template>
  <div
    class="mx-auto mb-6 w-full max-w-screen-xl"
    :class="[isFrontliner ? 'mt-3 pl-3 pr-6' : 'px-6']"
  >
    <slot />
  </div>
</template>
