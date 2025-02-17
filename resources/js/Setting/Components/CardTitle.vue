<template>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>
      <i :class="icon"></i> {{ title }}
    </h4>
    <slot></slot>
  </div>
</template>
<script setup>
defineProps({
  title: { type: String, default: '' },
  icon:  { type: String, default: '' }
})
</script>
