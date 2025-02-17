<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" id="Employee_change_password" aria-labelledby="form-offcanvasLabel">
      <FormHeader :createTitle="createTitle"></FormHeader>

      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <InputField type="password" class="col-md-12" :is-required="true" :label="$t('employee.lbl_password')"
              :placeholder="$t('customer.password')" v-model="password" :error-message="errors['password']"
                :error-messages="errorMessages['password']"></InputField>

              <InputField type="password" class="col-md-12" :is-required="true" :label="$t('employee.lbl_confirm_password')"
              :placeholder="$t('customer.confirm_password')" v-model="confirm_password" :error-message="errors['confirm_password']"
                :error-messages="errorMessages['confirm_password']"></InputField>
            </div>
          </div>
        </div>
      </div>
      <div class="offcanvas-footer border-top">
    <div class="d-grid d-md-flex gap-3 p-3">
      <button class="btn btn-primary d-block">
        <i class="fa-solid fa-floppy-disk"></i>
        {{ $t('messages.save') }}
      </button>
      <button class="btn btn-outline-primary d-block" type="button" @click="close()" data-bs-dismiss="offcanvas">
        <i class="fa-solid fa-angles-left"></i>
        {{ $t('messages.close') }}
      </button>
    </div>
  </div>
    </div>
  </form>
</template>
<script setup>
import { ref } from 'vue'

import { useField, useForm } from 'vee-validate'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { CHANGE_PASSWORD_URL } from '../constant/employee'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'

// props
defineProps({
  createTitle: { type: String, default: '' }
})

const { storeRequest } = useRequest()

const currentId = useModuleId(() => {}, 'employee_assign')

// Validations
const validationSchema = yup.object({
  password: yup.string().required('Password is a required field').min(8, 'Password must be at least 8 characters long'),
  confirm_password: yup
    .string()
    .required('Confirm password is a required field')
    .oneOf([yup.ref('password'), null], 'Passwords must match')
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    password: '',
    confirm_password: ''
  }
}

const setFormData = (data) => {
  resetForm({
    values: {
      password: '',
      confirm_password: ''
    }
  })
}

const close = () => {

  bootstrap.Offcanvas.getInstance('#Employee_change_password').hide()
    setFormData(defaultData())

}

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  values.employee_id = currentId.value
  storeRequest({ url: CHANGE_PASSWORD_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
})
// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#Employee_change_password').hide()
    setFormData(defaultData())
    currentId.value = 0
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}
</script>
