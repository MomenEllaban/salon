<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="0" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <InputField class="col-md-12" :is-required="true" :label="$t('page.lbl_name')" :placeholder="$t('service.enter_name')" v-model="title" :error-message="errors.title" :error-messages="errorMessages['title']"></InputField>
          </div>

          <div class="col-md-12">
            <label class="form-label">{{ $t('page.lbl_import') }} </label>
            <Multiselect id="import_role" v-model="import_role" :placeholder="$t('service.select_role')" :value="import_role" v-bind="RoleSelectOption" :options="role.options" class="form-group"></Multiselect>
            <span class="text-danger">{{ errors.default_time_zone }}</span>
          </div>
        </div>
      </div>
    <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { STORE_URL, GET_ROLE_LIST } from '../../constants/role_permission'
import { useField, useForm } from 'vee-validate'
import { useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import { createRequest, buildMultiSelectObject } from '@/helpers/utilities'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'

// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const RoleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false
})

const { storeRequest, listingRequest } = useRequest()

const type = 'role'
const role = ref([])
const getRoleList = () => {
  listingRequest({ url: GET_ROLE_LIST, data: { type: type } }).then((res) => {
    role.value.options = buildMultiSelectObject(res.results, {
      value: 'id',
      label: 'text'
    })
  })
}

onMounted(() => {
  getRoleList()
  setFormData(defaultData())
})

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    title: ''
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      title: data.title
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Offcanvas.getInstance('#form-offcanvas').hide()
    setFormData(defaultData())
    window.location.reload()
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const validationSchema = yup.object({
  title: yup.string().required('Name is a required field')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: title } = useField('title')
const { value: import_role } = useField('import_role')

const errorMessages = ref({})

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false

  storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
