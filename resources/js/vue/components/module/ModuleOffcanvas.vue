<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <div class="row">
            <div class="col-12">

              <InputField class="col-md-12" :is-required="true" :label="$t('page.lbl_name')"  placeholder="" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>


              <div class="col-md-12">
                 <label class="form-label">{{ $t('page.lbl_more_permission') }}</label>
                 <Multiselect id="more_permission" :multiple="true" v-model="more_permission" :value="more_permission" v-bind="MutipleSelectOption" :options="more_permission_data.options" class="form-group"></Multiselect>
                 <span class="text-danger">{{ errors.more_permission }}</span>
             </div>

              <div class="form-group">
                <div class="">
                  <label class="form-label" for="description">{{ $t('page.lbl_description') }}</label>
                    <textarea  class="form-control" v-model="description" id="description"></textarea>
                <span v-if="errorMessages['description']">
                  <ul class="text-danger">
                    <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                  </ul>
                </span>
                <span class="text-danger">{{ errors.description }}</span>

                </div>
              </div>

               <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                  <label class="form-label" for="page-status">{{ $t('page.lbl_status') }}</label>
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
  import { EDIT_URL, STORE_URL, UPDATE_URL,GET_PERMISSION_MODULE} from '../../constants/module'
  import { useField, useForm } from 'vee-validate'
  import { useModuleId, useRequest,useOnOffcanvasHide} from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
  import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { buildMultiSelectObject } from '@/helpers/utilities'

  // props
  defineProps({
    createTitle: { type: String, default: '' },
    editTitle: { type: String, default: '' }
  })

  const { getRequest, storeRequest, updateRequest,listingRequest } = useRequest()

  onMounted(() => {
    getPermissionList()
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
      description: '',
      more_permission: [],
      status: 1
    }
  }

  const more_permission_data = ref([])

const MutipleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false,
  createOption: true,
  mode: 'tags'
})


const type = 'additional_permissions'

const getPermissionList = () => {
  listingRequest({ url: GET_PERMISSION_MODULE, data: { type: type } }).then((res) => {
    more_permission_data.value.options = buildMultiSelectObject(res.results, {
      value: 'id',
      label: 'text'
    })
  })
}

  //  Reset Form
  const setFormData = (data) => {
    resetForm({
      values: {
        name: data.name,
        description: data.description,
        more_permission: data.more_permission,
        status: data.status

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

  const validationSchema = yup.object({
    name: yup.string()
      .required('Name is a required field') ,

  })

  const { handleSubmit, errors, resetForm } = useForm({
    validationSchema
  })
  const { value: name } = useField('name')
  const { value: description } = useField('description')
  const { value: more_permission } = useField('more_permission')
  const { value: status } = useField('status')
  const errorMessages = ref({})


  // Form Submit
  const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false

    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
    } else {
      storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
    }
  })

  useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

  </script>
