<template>
  <div class="mb-3">
    <label class="form-label"><strong>{{label}}</strong></label>
    <textarea class="form-control" id="settingFormControlTextarea" rows="6" :placeholder="placeholder" :value="modelValue" @input="updateModelValue"></textarea>
    <span class="text-danger">{{ errorMessage }}</span>
  </div>
</template>
<script setup>
defineProps({
  label: { type: String, default: '' },
  placeholder: { type: String, default: ''},
  modelValue: { type: String, default: ''},
  errorMessage: {type: String, default: ''}
})

const emit = defineEmits(['update:modelValue'])

const updateModelValue = (e) => emit('update:modelValue', e.target.value)
</script>

