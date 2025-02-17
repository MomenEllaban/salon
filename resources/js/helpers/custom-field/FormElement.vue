<template>
<div class="form-group">
    <label class="form-label">{{ label }}<span v-if="required"  class="text-danger">*</span></label>
    <input v-if="type === 'text' || type === 'number' || type === 'password' || type === 'date' || type === 'file'" :type="type" class="form-control" @input="updateModelValue" v-model="value" :name="name" :value="value" :required="required"  :field_id="field_id"/>
    <span class="text-danger"></span>
    <!-- <ul class="m-0">
      <li class="text-danger" v-for="msg in errorMessages" :key="msg">{{ msg }}</li>
    </ul> -->

  </div>
</template>

<script setup>
import { onMounted, watch, ref } from 'vue';

const props = defineProps({
  name: { type: String, default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: '' },
  required: { type: [Boolean, Number], default: false },
  modelValue: { type: String, default: '' },
  field_id:{type:Number, default: 0},
  options: { type: [Array, Object, String], default:() => [] },
});

const value = ref('');


const emit = defineEmits(['update:modelValue'])

onMounted(() => {
  value.value = props.modelValue[props.name]
});

watch(() => props.modelValue, () => {
  value.value = props.modelValue[`field_${props.field_id}`]
});

const updateModelValue = (e) => {
  let obj = typeof props.modelValue === 'object' ? {...props.modelValue} : {};

  obj[`field_${props.field_id}`] = value.value;
  emit('update:modelValue', obj);
};


</script>
