<template>
  <div class="form-group">
    <label class="form-label"
      ><strong>{{ label }}</strong></label
    >
    <input type="text" class="form-control" id="inputField" :value="modelValue" @input="updateModelValue" :placeholder="placeholder" />
    <span class="text-danger">{{ errorMessage }}</span>
    <p class="text-danger" v-for="msg in errorMessages">{{ msg }}</p>
  </div>
</template>

<script setup>
defineProps({
  isLabel: { type: Boolean, default: true },
  label: { type: String, default: '' },
  type: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

const updateModelValue = (e) => emit('update:modelValue', e.target.value)
</script>
