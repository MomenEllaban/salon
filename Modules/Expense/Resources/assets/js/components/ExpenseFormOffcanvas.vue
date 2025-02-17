<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <InputField class="col-md-12" :is-required="true" :label="$t('expense.lbl_title')" :placeholder="$t('expense.enter_title')" v-model="title" :error-message="errors.title" :error-messages="errorMessages['title']"></InputField>
              <InputField class="col-md-12" :is-required="true" :label="$t('expense.lbl_value')" :placeholder="$t('expense.enter_value')"  v-model="amount" :error-message="errors.amount" :error-messages="errorMessages['amount']"></InputField>
            </div>
          </div>
          <div class="col-12 my-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('expense.lbl_status') }}</label>
                <div class="form-check form-switch m-2">
                  <input class="form-check-input" :value="status" :true-value="1" :false-value="0" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
            </div>
        </div>
      </div>
    <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/expense'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const { getRequest, storeRequest, updateRequest } = useRequest()

onMounted(() => {
  setFormData(defaultData())
})

const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    title: '',
    amount: '',
    status: true
  }
}

const setFormData = (data) => {
  resetForm({
    values: {
      title: data.title,
      amount: data.amount,
      status: data.status,
    }
  })
}

const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const numberRegex = /^\d+$/;
const validationSchema = yup.object({
  title: yup.string()
    .required('Title is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  amount: yup.string()
    .required('Amount is a required field')
    .matches(/^\d+(\.\d+)?$/, 'Only numbers are allowed')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: title } = useField('title')
const { value: amount } = useField('amount')
const { value: status } = useField('status')

const errorMessages = ref({})

const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if (IS_SUBMITED.value) return false
  IS_SUBMITED.value = true

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>

<style>
.multiselect-clear {
    display: none !important;
}
</style>