<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" :placeholder="$t('state.enter_name')" type="text" :is-required="true" :label="$t('messages.name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
        <div class="col-md-12 form-group">
          <label class="form-label">{{ $t('branch.lbl_country') }} <span class="text-danger">*</span></label>
          <Multiselect id="units-list" :placeholder="$t('state.select_country')" v-model="country_id" :value="country_id" v-bind="singleSelectOption" :options="country.options" class="form-group"></Multiselect>
          <span class="text-danger">{{ errors['country_id'] }}</span>
        </div>
        <div class="form-group">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="category-status">{{ $t('service.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
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
import { EDIT_URL, STORE_URL, UPDATE_URL,COUNTRY_URL } from '../constant/state'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'

// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

// Edit Form Or Create Form
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
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))


/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    status: true,
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      status: data.status,
      country_id: data.country_id
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

// Validations
const validationSchema = yup.object({
  name: yup.string().required('Name is a required field'),
  country_id: yup.string().required('country is a required field'),
})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: status } = useField('status')
const { value: country_id  } = useField('country_id')

const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})


const singleSelectOption = ref({
  createOption: true,
  closeOnSelect: true,
  searchable: true
})


const country = ref({ options: [], list: [] })

useSelect({ url: COUNTRY_URL }, { value: 'id', label: 'name' }).then((data) => (country.value = data))


// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true;
  values.custom_fields_data = JSON.stringify(values.custom_fields_data);
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values })
      .then((res) => reset_datatable_close_offcanvas(res))
      .finally(() => {
        // Reset the flag to allow further submissions
        isSubmitting = false;
      });
  } else {
    storeRequest({ url: STORE_URL, body: values })
      .then((res) => reset_datatable_close_offcanvas(res))
      .finally(() => {
        // Reset the flag to allow further submissions
        isSubmitting = false;
      });
  }
});

</script>
