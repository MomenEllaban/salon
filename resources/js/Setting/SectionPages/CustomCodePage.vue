<style></style>
<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_custom_code')" icon="fa-solid fa-file-code"></CardTitle>
    </div>
    <TextArea :label="$t('setting_custom_code.lbl_css_name')" placeholder="custom css code" v-model="custom_css_block" :errorMessage="errors.custom_css_block"> </TextArea>
    <TextArea :label="$t('setting_custom_code.lbl_js_name')" placeholder="custom js code" v-model="custom_js_block" :errorMessage="errors.custom_js_block"> </TextArea>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import TextArea from '@/Setting/Components/TextArea.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'

import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()

const IS_SUBMITED = ref(false)

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      custom_css_block: data.custom_css_block,
      custom_js_block: data.custom_js_block
    }
  })
}

// Validations
const validationSchema = yup.object({
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

const { value: custom_css_block } = useField('custom_css_block')
const { value: custom_js_block } = useField('custom_js_block')

//fetch data
const data = 'custom_css_block,custom_js_block'

onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  storeRequest({ url: STORE_URL, body: values }).then((res) => display_submit_message(res))
})
</script>
