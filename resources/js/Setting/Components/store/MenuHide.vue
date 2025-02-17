<template>
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="mt-4 mb-3">{{ $t('customization.menu_hide') }}</h5>
    <div class="form-check form-switch">
      <checkbox-input btn-name="sidebar_show" type="switch" label-title="Mini" label-class="overflow-hidden p-0" id="sidebar-hide" :defaultChecked="['sidebar','sidebar-none']" :value="store.setting.sidebar_show.value == 'sidebar' ? 'sidebar-none' : 'sidebar'" @onChange="onUpdate"> </checkbox-input>
    </div>
  </div>
</template>
<script setup>
import {useSetting} from '@/store/index'
import CheckboxInput from '../store/elements/CheckboxInput.vue'
const store = useSetting()
const onUpdate = (value) => {
  store.sidebar_show(value[0])
}
</script>
