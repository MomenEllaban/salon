<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('service.lbl_name')" placeholder="" v-model="name" :error-message="errors['name']" ></InputField>
          <div class="form-group">
            <label class="form-label" for="type"> {{ $t('custom_feild.lbl_type') }} <span class="text-danger">*</span></label>
            <Multiselect v-model="type" :value="type" v-bind="type_data" id="type" autocomplete="off"></Multiselect>
            <span v-if="errorMessages['type']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['type']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.type }}</span>
          </div>
          <div class="form-group" v-if="type">
            <div v-for="(input, index) in variationValues" :key="index" class="d-flex gap-3 align-items-end">
              <div v-if="type == 'color'">
                <label class="form-label" for="label">{{ $t('service.lbl_colour') }}</label>
                <input type="color" class="w-100" v-model="input.value.value" />
              </div>
              <div v-else-if="type == 'text'">
                <label class="form-label" for="label">{{ $t('service.lbl_value') }}</label>
                <input type="text" class="form-control" v-model="input.value.value" />
              </div>
              <div class="flex-grow w-100">
                <label class="form-label" for="label">{{ $t('service.lbl_name') }}</label>
                <input type="text" class="form-control" v-model="input.value.name" />
              </div>
              <a v-if="variationValues.length > 1" class="btn btn-primary" @click="remove(index)">{{ $t('service.lbl_delete') }}</a>
            </div>
            <div class="my-3">
              <button class="btn btn-secondary w-100" type="button" @click="push({value: '', name: ''})"><i class="fa fa-plus-circle" aria-hidden="true"></i> {{ $t('service.add_values') }}</button>
            </div>
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
  import { ref, onMounted, reactive } from 'vue'
  import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/variation'
  import { useField, useForm, useFieldArray } from 'vee-validate'
  import InputField from '@/vue/components/form-elements/InputField.vue'

  import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
  import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

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

  const type_data = ref({
  searchable: true,
  options: [
    { label: 'Text', value: 'text' },
    { label: 'Color', value: 'color' }
  ],
  closeOnSelect: true
})

  /*
   * Form Data & Validation & Handeling
   */
  // Default FORM DATA
  const defaultData = () => {
    errorMessages.value = {}
    return {
      name: '',
      type: '',
      status: true,
      values: []
    }
  }

  //  Reset Form
  const setFormData = (data) => {
    resetForm({
      values: {
        name: data.name,
        type: data.type,
        status: data.status,
        values: data.values
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
      removeImage()
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
  }

  // Validations
  const validationSchema = yup.object({
    name: yup.string().required('Name is a required field'),
  })


  const { handleSubmit, errors, resetForm } = useForm({
    validationSchema
  })
  const { value: name } = useField('name')
  const { value: status } = useField('status')
  const { value: type } = useField('type')

  const {fields: variationValues, push, remove} = useFieldArray('values');
  push({value: '', name: ''})

  const errorMessages = ref({})

  onMounted(() => {
    setFormData(defaultData())
  })
  const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true
    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values })
        .then((res) => reset_datatable_close_offcanvas(res));
    } else {
      storeRequest({ url: STORE_URL, body: values })
        .then((res) => reset_datatable_close_offcanvas(res));
    }
  });

  </script>
<style scoped>
[type="color"] {
  width: 100%;
  height: 2.5rem;
  border-radius: var(--bs-border-radius);
}
</style>
