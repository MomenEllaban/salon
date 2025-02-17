<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label class="form-label" for="name">{{ $t('plan_limitation.lbl_name') }}</label>
              <input type="text" class="form-control" v-model="name" id="name" />
              <span v-if="errorMessages['name']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['name']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.name }}</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="limit">{{ $t('plan_limitation.lbl_set_limit') }}</label>
              <input type="number" class="form-control" v-model="limit" id="limit" />
              <span v-if="errorMessages['limit']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['limit']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.limit }}</span>
            </div>
            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('plan_limitation.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
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
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/planlimit'
import { useField, useForm } from 'vee-validate'

import { useModuleId, useRequest,useOnOffcanvasHide} from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

onMounted(() => {

  setFormData(defaultData())
})

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => res.status && setFormData(res.data))
  } else {
    setFormData(defaultData())
  }
})

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    limit: '',
    status: 1
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      limit: data.limit,
      status: data.status,
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

const numberRegex = /^\d+$/;
// Validations
const validationSchema = yup.object({
  name: yup.string()
    .required('Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  limit: yup.string()
    .required('Set Limit is a required field')
    .matches(/^\d+$/, 'Only numbers are allowed')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: limit } = useField('limit')
const { value: status } = useField('status')
const errorMessages = ref({})

const categories = ref({
  searchable: true,
  options: [],
  createOption: true,
  closeOnSelect: true
})

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

</script>
