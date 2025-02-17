<template>
  <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
    <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
    <form @submit="formSubmit">
      <div class="offcanvas-body">
            <InputField class="col-md-12" type="text" :is-required="true" :label="$t('constant.lbl_type')" placeholder="" v-model="type" :error-message="errors['type']" :error-messages="errorMessages['type']"></InputField>
            <InputField class="col-md-12" type="text" :is-required="true" :label="$t('constant.lbl_name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
            <InputField class="col-md-12" type="text" :is-required="true" :label="$t('constant.lbl_value')" placeholder="" v-model="value" :error-message="errors['value']" :error-messages="errorMessages['value']"></InputField>
            <div class="form-group">
              <label class="form-label" for="name">{{ $t('constant.lbl_sub_type') }}</label>
              <input type="text" class="form-control" v-model="sub_type" id="sub_type" />
              <span v-if="errorMessages['sub_type']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['sub_type']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.sub_type }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="name">{{ $t('constant.lbl_sequence') }}</label>
              <input type="number" class="form-controstatus" v-model="sequence" id="sub_type" />
              <span v-if="errorMessages['sequence']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['sequence']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.sequence }}</span>
            </div>

            <!-- <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="constant-status">Status</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="1" name="status" id="constant-status" type="checkbox" v-model="status" />
                </div>
              </div>
              <span v-if="errorMessages['status']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['status']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.status }}</span>
            </div> -->

            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="status">{{ $t('constant.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
              <!-- <span v-if="errorMessages['status']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['status']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.status }}</span> -->
            </div>
        </div>
    <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </form>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup';

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// props
defineProps({
  createTitle: {
    type: String,
    default: ''
  },
  editTitle: {
    type: String,
    default: ''
  }
})


const { getRequest, storeRequest, updateRequest } = useRequest()


// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => res.status && setFormData(res.data))
  } else {
    setFormData(defaultData())
  }
})

const numberRegex = /^\d+$/;
// Vee-Validation Validations
const validationSchema = yup.object({
  name: yup.string()
    .required('Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  type: yup.string()
    .required('Type is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  value: yup.string().required('Value is a required field'),
  status: yup.boolean()
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const { value: type } = useField('type')
const { value: name } = useField('name')
const { value: value } = useField('value')
const { value: sub_type } = useField('sub_type')
const { value: sequence } = useField('sequence')
const { value: status } = useField('status')
const errorMessages = ref({})

// Api Call for EDIT, STORE, UPDATE REQUEST FUNCTION

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    type: '',
    name: '',
    value: '',
    sub_type: '',
    sequence: 0,
    status : true,
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      type: data.type,
      name: data.name,
      value: data.value,
      sub_type: data.sub_type,
      sequence: data.sequence,
      status: data.status ? true : false
    }
  })
}


// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
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

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>
