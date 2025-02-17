<template>
  <div>
    <h6 class="mt-4 mb-3">{{ $t('customization.navbar_style') }}</h6>
    <div class="d-grid gap-3 grid-cols-4 mb-4">
      <radio-input btn-name="header_navbar" labelClass="d-block" label-title="{{ $t('customization.glass') }}" class-name="text-center" id="nav-glass" :default-checked="headerNavbar" value="nav-glass" @onChange="store.header_navbar">{{ $t('customization.glass') }}</radio-input>
      <radio-input btn-name="header_navbar" labelClass="d-block" label-title="{{ $t('customization.sticky') }}" class-name="text-center" id="navs-sticky" :default-checked="headerNavbar" value="navs-sticky" @onChange="store.header_navbar">{{ $t('customization.sticky') }}</radio-input>
      <radio-input btn-name="header_navbar" labelClass="d-block" label-title="{{ $t('customization.transparent') }}" class-name="text-center" id="navs-transparent" :default-checked="headerNavbar" value="navs-transparent" @onChange="store.header_navbar">{{ $t('customization.transparent') }}</radio-input>
      <radio-input btn-name="header_navbar" labelClass="d-block" label-title="{{ $t('customization.default') }}" class-name="text-center" id="navs-default" :default-checked="headerNavbar" value="navs-default" @onChange="store.header_navbar">{{ $t('customization.default') }}</radio-input>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useSetting } from '@/store/index'
import RadioInput from '../store/elements/RadioInput.vue'

const store = useSetting()
const headerNavbar = computed(() => store.header_navbar_value)

</script>
