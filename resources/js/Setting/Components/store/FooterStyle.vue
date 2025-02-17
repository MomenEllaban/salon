<template>
  <div class="mt-4 mb-3">
    <h6 class="d-inline-block mb-0 me-2">{{ $t('customization.footer') }}</h6>
  </div>
  <div class="d-grid gap-3 grid-cols-3 mb-4">
    <radio-input btn-name="footer_style" id="footer_Sticky" labelClass=" d-block" :defaultChecked="footerStyle" value="sticky" @onChange="store.footer_style"> {{ $t('customization.sticky') }} </radio-input>
    <radio-input btn-name="footer_style" id="footer_default" labelClass=" d-block" :defaultChecked="footerStyle" value="default" @onChange="store.footer_style"> {{ $t('customization.default') }} </radio-input>
    <radio-input btn-name="footer_style" id="footer_glass" labelClass=" d-block" :defaultChecked="footerStyle" value="glass" @onChange="store.footer_style">  {{ $t('customization.glass') }}</radio-input>
  </div>
</template>
<script setup>
import {computed} from 'vue'
import {useSetting} from '@/store/index'
import RadioInput from '../store/elements/RadioInput.vue'
const store = useSetting()
const footerStyle = computed(() => store.footer_style_value)
</script>
