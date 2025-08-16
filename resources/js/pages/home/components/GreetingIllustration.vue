<script setup lang="ts">
import { SharedData } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { GreetingKey } from '..'

interface GreetingIllustrationProps {
  dayPart: GreetingKey
  illustration: string
}

defineProps<GreetingIllustrationProps>()

const page = usePage<SharedData>()
const employeeFirsName = page.props.auth.user.employee.first_name

const greetings: Record<GreetingKey, { label: string; color: string }> = {
  morning: {
    label: `Good Morning, ${employeeFirsName}!`,
    color: 'text-lime-700',
  },
  afternoon: {
    label: `Good afternoon, ${employeeFirsName}!`,
    color: 'text-lime-700',
  },
  evening: {
    label: `Good evening, ${employeeFirsName}!`,
    color: 'text-white',
  },
}
</script>

<template>
  <div class="relative h-[173px] rounded-lg shadow">
    <div class="absolute inset-0">
      <img
        :src="illustration"
        loading="eager"
        alt="Greeting Illustration"
        class="h-full w-full rounded-lg object-cover"
      />
    </div>
    <div class="absolute inset-0 flex flex-col justify-start px-6 pt-12">
      <div :class="['text-3xl font-bold', greetings[dayPart].color]">
        {{ greetings[dayPart].label }}
      </div>
      <div class="text-base text-white">Here's what's happening today!</div>
    </div>
  </div>
</template>
