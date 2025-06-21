<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
  <svg width="170" height="170" viewBox="0 0 40 40">
    <defs>
      <pattern id="imgpattern" patternUnits="userSpaceOnUse" width="40" height="40">
        <image href="/mst-logo-noBG.png" x="0" y="0" width="40" height="40" />
      </pattern>
    </defs>
    <rect width="40" height="40" fill="url(#imgpattern)" />
  </svg>
</template>
