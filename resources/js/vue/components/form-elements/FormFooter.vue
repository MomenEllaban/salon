<template>
  <div class="offcanvas-footer border-top">
    <div class="d-grid d-md-flex gap-3 p-3">
      <button :disabled="IS_SUBMITED" class="btn btn-primary" name="submit">
        <template v-if="IS_SUBMITED">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Loading...
        </template>
        <template v-else> <i class="fa-solid fa-floppy-disk"></i> {{ $t('messages.save') }}</template>
      </button>
      <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas">
        <i class="fa-solid fa-angles-left"></i>
        {{ $t('messages.close') }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  IS_SUBMITED: false
})
</script>

