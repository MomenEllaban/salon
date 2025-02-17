<template>
  <div>
    <h6 class="mb-3">{{ $t('customization.menu_style') }}</h6>
    <div class="d-grid gap-3 grid-cols-4 mb-4">
      <checkbox-input btn-name="sidebar_type" label-title="Mini" id="sidebar-mini" label-class="d-block" :default-checked="sidebarType"  value="sidebar-mini" @onChange="store.sidebar_type">{{ $t('customization.mini') }}</checkbox-input>
      <checkbox-input btn-name="sidebar_type" label-title="Hover" id="sidebar-hover" label-class="d-block"  :default-checked="sidebarType" value="sidebar-hover" @onChange="store.sidebar_type">{{ $t('customization.hover') }}</checkbox-input>
      <checkbox-input btn-name="sidebar_type" label-title="Boxed" id="sidebar-boxed" label-class="d-block"  :default-checked="sidebarType" value="sidebar-boxed" @onChange="store.sidebar_type">{{ $t('customization.boxed') }}</checkbox-input>
      <checkbox-input btn-name="sidebar_type" label-title="Soft" id="sidebar-soft" label-class="d-block"  :default-checked="sidebarType" value="sidebar-soft" @onChange="store.sidebar_type">{{ $t('customization.soft') }}</checkbox-input>
    </div>
  </div>
</template>
<script setup>
import {computed} from 'vue'
import {useSetting} from '@/store/index'
import CheckboxInput from '../store/elements/CheckboxInput.vue'
const store = useSetting()
const sidebarType = computed(() => store.sidebar_type_value)
</script>
