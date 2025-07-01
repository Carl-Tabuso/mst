<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthBase from '@/layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'
import { computed } from 'vue'

defineProps<{
  status?: string
  canResetPassword: boolean
}>()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

// Initial form validation enabling
const isValid = computed(() => {
  const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)
  return emailValid && form.password.length >= 8
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <AuthBase
    title="Log in to your account"
    description="Enter your email and password below to log in"
  >
    <Head title="Log in" />

    <form
      @submit.prevent="submit"
      class="mx-auto flex h-full w-full max-w-xs flex-col gap-2 px-4 pb-8 pt-4 sm:max-w-sm sm:px-6 sm:pb-10 sm:pt-6 md:max-w-md md:px-12 md:pb-12 md:pt-8 lg:max-w-lg lg:px-16 lg:pb-16 lg:pt-10"
    >
      <div class="space-y-2">
        <div>
          <Label
            for="email"
            class="mb-0.5 block text-[11px] font-medium text-neutral-700 sm:text-xs md:text-sm"
          >
            Email Address <span class="text-red-500">*</span>
          </Label>
          <Input
            id="email"
            type="email"
            required
            autofocus
            autocomplete="email"
            v-model="form.email"
            class="w-full px-2 py-1 text-[11px] sm:text-xs md:text-sm"
            placeholder="email@example.com"
          />
          <InputError
            :message="form.errors.email"
            class="text-[10px] sm:text-xs md:text-sm"
          />
        </div>

        <div>
          <div class="mb-0.5 flex items-center justify-between">
            <Label
              for="password"
              class="text-[11px] font-medium text-neutral-700 sm:text-xs md:text-sm"
            >
              Password <span class="text-red-500">*</span>
            </Label>
          </div>
          <Input
            id="password"
            type="password"
            required
            autocomplete="current-password"
            v-model="form.password"
            class="w-full px-2 py-1 text-[11px] sm:text-xs md:text-sm"
            placeholder="Password"
          />
          <InputError
            :message="form.errors.password"
            class="text-[10px] sm:text-xs md:text-sm"
          />
        </div>

        <div class="flex justify-end">
          <TextLink
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-[10px] text-neutral-700 hover:text-neutral-900 hover:underline sm:text-xs md:text-sm"
          >
            Forgot password?
          </TextLink>
        </div>

        <div class="flex items-center space-x-1">
          <Checkbox
            id="remember"
            class="size-2 border-neutral-700 sm:size-3"
            v-model:checked="form.remember"
          />
          <Label
            for="remember"
            class="text-[10px] text-neutral-700 sm:text-xs md:text-sm"
            >Remember me</Label
          >
        </div>

        <Button
          type="submit"
          :class="[
            'w-full bg-blue-900 py-1.5 text-[11px] text-white hover:bg-blue-800 sm:text-xs md:text-sm',
            form.processing || !isValid ? 'cursor-not-allowed opacity-60' : '',
          ]"
          :disabled="form.processing || !isValid"
        >
          <LoaderCircle
            v-if="form.processing"
            class="mr-1 h-3 w-3 animate-spin"
          />
          Log in
        </Button>
      </div>
    </form>
  </AuthBase>
</template>
