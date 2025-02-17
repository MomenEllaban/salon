<template>
  <div class="mb-3">
    <label class="form-label"><strong>{{label}}</strong></label>
    <input type="password" class="form-control" id="inputField" :value="modelValue" @input="updateModelValue" :placeholder="placeholder">
    <span class="text-danger">{{ errorMessage }}</span>
  </div>
</template>

<script setup>
defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: ''},
  placeholder: { type: String, default: '' },
  errorMessage: {type: String, default: ''}
})

const emit = defineEmits(['update:modelValue'])

const updateModelValue = (e) => emit('update:modelValue', e.target.value)
</script>
