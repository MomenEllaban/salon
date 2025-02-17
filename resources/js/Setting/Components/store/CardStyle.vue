<template>
  <div class="mt-4 mb-3">
    <h6 class="d-inline-block mb-0 me-2">{{ $t('customization.card_style') }}</h6>
  </div>
  <div class="d-grid gap-3 grid-cols-3 mb-3">
    <radio-input btn-name="card_style" id="card-default" labelClass=" d-block" :default-checked="cardStyle" value="card-default" @onChange="store.card_style">{{ $t('customization.default') }}  </radio-input>
    <radio-input btn-name="card_style" id="card-glass" labelClass=" d-block" :default-checked="cardStyle" value="card-glass" @onChange="store.card_style"> {{ $t('customization.glass') }} </radio-input>
    <radio-input btn-name="card_style" id="card-transparent" labelClass=" d-block" :default-checked="cardStyle" value="card-transparent" @onChange="store.card_style">{{ $t('customization.transparent') }}  </radio-input>
  </div>
</template>
<script setup>
import {computed} from 'vue'
import {useSetting} from '@/store/index'
import RadioInput from '../store/elements/RadioInput.vue'

const store = useSetting()
const cardStyle = computed(() => store.card_style_value)
</script>
