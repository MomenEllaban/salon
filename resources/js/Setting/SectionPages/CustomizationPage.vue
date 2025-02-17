<template>
  <div>
    <CardTitle :title="$t('setting_sidebar.lbl_customization')" icon="fa-solid fa-swatchbook"></CardTitle>
  </div>
  <color-customizer></color-customizer>
  <!-- <font-family></font-family> -->
  <navbar-style></navbar-style>
  <navbar-hide></navbar-hide>
  <card-style></card-style>
  <menu-color></menu-color>
  <menu-style></menu-style>
  <menu-active-style></menu-active-style>
  <menu-hide></menu-hide>
  <footer-style></footer-style>
  <SubmitButton :IS_SUBMITED="IS_SUBMITED" @click="onSubmit"></SubmitButton>
</template>

<script setup>
import CardStyle from '@/Setting/Components/store/CardStyle.vue'
import FooterStyle from '@/Setting/Components/store/FooterStyle.vue'
import NavbarStyle from '@/Setting/Components/store/NavbarStyle.vue'
import NavbarHide from '@/Setting/Components/store/NavbarHide.vue'
import MenuColor from '@/Setting/Components/store/MenuColor.vue'
import MenuStyle from '@/Setting/Components/store/MenuStyle.vue'
import MenuActiveStyle from '@/Setting/Components/store/MenuActiveStyle.vue'
import ColorCustomizer from '@/Setting/Components/store/ColorCustomizer.vue'
import MenuHide from '@/Setting/Components/store/MenuHide.vue'
// import FontFamily from '@/Setting/Components/store/FontFamily.vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { useSetting } from '@/store/index'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
import { createRequest } from '@/helpers/utilities'
import { onMounted, ref } from 'vue'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
const store = useSetting()
// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}
const onSubmit = () => {
  IS_SUBMITED.value = true
  const settings = store.setting
  const settingObject = {
    saveLocal: 'sessionStorage',
    storeKey: 'frekza-setting',
    setting: settings
  }
  const storeState = {
    customization_json: JSON.stringify(settingObject),
    root_colors: sessionStorage.getItem('root_colors')
  }

  storeRequest({ url: STORE_URL, body: storeState }).then((res) => display_submit_message(res))
}

onMounted(() => {
  createRequest(GET_URL('customization_json')).then((response) => {
    if (response.customization_json) {
      const json = JSON.parse(response.customization_json)
      if (json) {
        const setting = json.setting
        store.setSetting(setting)
      }
    }
  })
})
</script>
