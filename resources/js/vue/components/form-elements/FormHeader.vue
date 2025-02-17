<template>
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="form-offcanvasLabel">
      <template v-if="currentId != 0">
        <span>{{ editTitle }}</span>
      </template>
      <template v-else>
        <span>{{ createTitle }}</span>
      </template>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
</template>

<script setup>
defineProps({
  currentId: { type: Number, default: 0 },
  editTitle: { type: String, default: '' },
  createTitle: { type: String, default: '' }
})
</script>
