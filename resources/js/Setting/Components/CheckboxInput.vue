<template>
 <div class="form-check">
  <label>
      <input name="checkbox" class="form-check-input" value="1" type="checkbox" checked="checked">
      <label class="form-label"><strong>{{label}}</strong></label>
      <span class="text-danger">{{ errorMessage }}</span>
  </label>
</div>
</template>

<script setup>
defineProps({ label: { type: String, default: '' },
              modelValue: { type: String, default: ''},
              errorMessage: {type: String, default: ''}
})

const emit = defineEmits(['update:modelValue'])

const updateModelValue = (e) => emit('update:modelValue', e.target.value)

</script>

