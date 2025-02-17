<template>
  <form @submit="formSubmit">
    <CardTitle :title="$t('setting_sidebar.lbl_inv_setting')" icon="fa-solid fa-file-invoice"></CardTitle>
    <div class="row">
      <InputField class="col-md-6" :is-required="false" :label="$t('setting_invoice.lbl_order_prefix')" placeholder="# - INV" v-model="inv_prefix" :error-message="errors.inv_prefix"></InputField>
      <InputField class="col-md-6" type="number" :is-required="false" :label="$t('setting_invoice.lbl_order_starts')" placeholder="Enter Order Starts" v-model="order_code_start" :error-message="errors.order_code_start"></InputField>

      <div class="col-12 position-relative">
        <InputField :is-required="false" :label="$t('setting_invoice.lbl_spacial_note')" placeholder="Enter your special note" v-model="spacial_note" :error-message="errors.spacial_note"></InputField>
        <div
          :style="{
            position: 'absolute',
            bottom: '8px',
            right: '12px',
            fontSize: '0.8rem',
            color: 'gray',
            backgroundColor: 'white',
            padding: '0 4px',
            pointerEvents: 'none'
          }"
        >
          {{ spacialNoteLength }}/191
        </div>
      </div>

    <div class="form-group col-md-12">
      <label for="" class="w-100">Template</label>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="template" v-model="template" id="template1" value="template1">
        <label class="form-check-label" for="template1">
        template1
        </label>
        </div>
        <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="template" v-model="template" id="template2" value="template2">
        <label class="form-check-label" for="template2">
        template2
        </label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="template" v-model="template" id="template3" value="template3">
          <label class="form-check-label" for="template3">
        template3
        </label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="template" v-model="template" id="template4" value="template4">
          <label class="form-check-label" for="template4">
        template4
        </label>
        </div>
        </div>
    </div>
    <div class="d-grid d-md-flex gap-3 align-items-center">
      <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'

import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      inv_prefix: data.inv_prefix,
      order_code_start: data.order_code_start,
      spacial_note: data.spacial_note,
      template: data.template,

    }
  })
}

//validation
const validationSchema = yup.object({
  inv_prefix: yup.string().required('Invoice Prefix Is Required'),
  order_code_start: yup.number().required('Order Starts Is Required').min(1, 'Order Starts must be greater than or equal to 1').typeError('Order Starts must be a valid number'),
  spacial_note: yup.string().required('Special Note Is Required'),
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

const { value: inv_prefix } = useField('inv_prefix')
const { value: order_code_start } = useField('order_code_start')
const { value: spacial_note } = useField('spacial_note')
const { value: template } = useField('template')

const spacialNoteLength = computed(() => spacial_note.value ? spacial_note.value.length : 0)

//fetch data
const data = 'inv_prefix,order_code_start,spacial_note,template'

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
