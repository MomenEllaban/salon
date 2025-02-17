<template>
  <h6 class="mt-4 mb-3">{{ $t('customization.menu_color') }}</h6>
  <div class="d-grid gap-3 grid-cols-3 mb-3">
    <radio-input btn-name="sidebar_color" labelClass="d-flex align-items-center bg-transparent gap-1" id="sidebar-white" :default-checked="sidebarColor" value="sidebar-white" @onChange="store.sidebar_color">
      <i class="text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" fill="currentColor" stroke="black" stroke-width="3"></circle>
        </svg>
      </i>
      <span>  {{ $t('customization.default') }}</span>
    </radio-input>
    <radio-input btn-name="sidebar_color" labelClass="d-flex align-items-center bg-transparent gap-1" id="sidebar-dark" :default-checked="sidebarColor" value="sidebar-dark" @onChange="store.sidebar_color">
      <i class="text-dark">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
        </svg>
      </i>
      <span> {{ $t('customization.dark') }} </span>
    </radio-input>
    <radio-input btn-name="sidebar_color" labelClass="d-flex align-items-center bg-transparent gap-1" id="sidebar-color" :default-checked="sidebarColor" value="sidebar-color" @onChange="store.sidebar_color">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" fill="currentColor"></circle>
        </svg>
      <span>  {{ $t('customization.color') }}</span>
    </radio-input>
  </div>
  <div class="d-grid gap-3 grid-cols-2 mb-4">
    <radio-input btn-name="sidebar_color" labelClass="d-flex align-items-center bg-transparent gap-1" :imgComponent="true" :default-checked="sidebarColor" id="sidebar-transparent" value="sidebar-transparent" @onChange="store.sidebar_color">
      <i class="text-dark">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" fill="#F5F6FA" stroke="black" stroke-width="3"></circle>
        </svg>
      </i>
      <span> {{ $t('customization.transparent') }} </span>
    </radio-input>
    <radio-input btn-name="sidebar_color" labelClass="d-flex align-items-center bg-transparent gap-1" :imgComponent="true" :default-checked="sidebarColor" id="sidebar-glass" value="sidebar-glass" @onChange="store.sidebar_color">
      <i class="text-dark">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" fill="#F5F6FA" stroke="black" stroke-width="3"></circle>
        </svg>
      </i>
      <span>  {{ $t('customization.glass') }}  </span>
    </radio-input>
  </div>
</template>
<script setup>
import {computed} from 'vue'
import {useSetting} from '@/store/index'
import RadioInput from '../store/elements/RadioInput.vue'
const store = useSetting()
const sidebarColor = computed(() => store.sidebar_color_value)
</script>
