<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance'
import { computed } from 'vue'
import Icon from './Icon.vue'
import { Switch } from './ui/switch'

const { appearance, updateAppearance } = useAppearance()

const isDarkMode = computed(() => appearance.value === 'dark')

const toggleTheme = () => {
  updateAppearance(isDarkMode.value ? 'light' : 'dark')
}
</script>

<template>
  <Switch
    :model-value="isDarkMode"
    @update:model-value="toggleTheme"
  >
    <template #thumb>
      <div class="flex h-full w-full items-center justify-center">
        <Icon
          v-if="!isDarkMode"
          name="sun"
        />
        <Icon
          v-else
          name="moon"
        />
      </div>
    </template>
  </Switch>
</template>
