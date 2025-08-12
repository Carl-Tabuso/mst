<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

interface Home1Props { 
    greeting: string
    illustration: string
}

defineProps<Home1Props>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Home',
    href: '/',
  },
]
</script>

<template>
    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto mb-6 mt-3 w-full max-w-screen-xl px-6">
            <div class="flex lg:flex-row sm:flex-col items-center gap-4">
                <div class="flex flex-col gap-2">
                    <div class="shadow w-[844px] h-[173px] relative rounded-lg">
                        <img
                            :src="illustration"
                            loading="lazy"
                            alt="Greeting Illustration"
                            class="absolute inset-0 w-full h-full rounded-lg object-cover will-change-transform"
                        />
                        <div class="absolute inset-0 flex flex-col justify-start pt-12 px-6">
                            <div class="text-white font-bold text-3xl">
                                {{ greeting }}
                            </div>
                            <div class="text-white text-base">
                                Here's what's happening today!
                            </div>                                
                        </div>                        
                    </div>
                    <div class="flex"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
