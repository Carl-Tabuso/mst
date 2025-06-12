<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Initial form validation enabling
const isValid = computed(() => {
    const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
    return emailValid && form.password.length > 8;
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your email and password below to log in">
        <Head title="Log in" />

        <form
          @submit.prevent="submit"
          class="flex flex-col gap-2 h-full
                 px-4 pb-8 pt-4
                 sm:px-6 sm:pb-10 sm:pt-6
                 md:px-12 md:pb-12 md:pt-8
                 lg:px-16 lg:pb-16 lg:pt-10
                 w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto"
        >
          <div class="space-y-2">
            <div>
              <Label for="email" class="block text-[11px] sm:text-xs md:text-sm font-medium text-neutral-700 mb-0.5">
                Email Address <span class="text-red-500">*</span>
              </Label>
              <Input
                id="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                v-model="form.email"
                class="w-full text-[11px] sm:text-xs md:text-sm py-1 px-2"
                placeholder="email@example.com"
              />
              <InputError :message="form.errors.email" class="text-[10px] sm:text-xs md:text-sm" />
            </div>

            <div>
              <div class="flex justify-between items-center mb-0.5">
                <Label for="password" class="text-[11px] sm:text-xs md:text-sm font-medium text-neutral-700">
                  Password <span class="text-red-500">*</span>
                </Label>
              </div>
              <Input
                id="password"
                type="password"
                required
                autocomplete="current-password"
                v-model="form.password"
                class="w-full text-[11px] sm:text-xs md:text-sm py-1 px-2"
                placeholder="Password"
              />
              <InputError :message="form.errors.password" class="text-[10px] sm:text-xs md:text-sm" />
            </div>

            <div class="flex justify-end">
              <TextLink
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-[10px] sm:text-xs md:text-sm text-neutral-700 hover:underline hover:text-neutral-900"
              >
                Forgot password?
              </TextLink>
            </div>

            <div class="flex items-center space-x-1">
              <Checkbox id="remember" class="size-2 sm:size-3 border-neutral-700" v-model:checked="form.remember" />
              <Label for="remember" class="text-[10px] sm:text-xs md:text-sm text-neutral-700">Remember me</Label>
            </div>

            <Button
              type="submit"
              :class="[
                'w-full bg-blue-900 text-white text-[11px] sm:text-xs md:text-sm py-1.5 hover:bg-blue-800',
                (form.processing || !isValid) ? 'opacity-60 cursor-not-allowed' : ''
              ]"
              :disabled="form.processing || !isValid"
            >
              <LoaderCircle v-if="form.processing" class="h-3 w-3 animate-spin mr-1" />
              Log in
            </Button>
          </div>
        </form>
    </AuthBase>
</template>