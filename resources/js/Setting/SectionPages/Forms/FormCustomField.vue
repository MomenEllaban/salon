<template>
  <form @submit="formSubmit">
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen-md-down">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="offcanvas-title">
              <template v-if="currentId != 0">
                <span>{{ $t('custom_feild.lbl_edit') }}</span>
              </template>
              <template v-else>
                <span> {{ $t('custom_feild.lbl_add') }}</span>
              </template>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label class="form-label" for="type">{{ $t('custom_feild.lbl_module') }}<span class="text-danger">*</span></label>
                  <Multiselect v-model="Module" :value="Module" v-bind="module_data" id="Module" autocomplete="off"></Multiselect>
                  <span v-if="errorMessages['Module']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['Module']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.Module }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label" for="label">{{ $t('custom_feild.lbl_label') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="label" id="label" />
                  <span v-if="errorMessages['label']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['label']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.label }}</span>
                </div>
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
                <div class="form-group">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="category-status">{{ $t('custom_feild.lbl_is_required') }} </label>
                    <div class="form-check form-switch">
                      <input class="form-check-input" :value="1" name="required" id="required" type="checkbox" v-model="required" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="category-status"> {{ $t('custom_feild.lbl_allow_table_view') }} </label>
                    <div class="form-check">
                      <input class="form-check-input" :value="1" name="is_export" id="is_export" type="checkbox" v-model="is_export" />
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="category-status"> {{ $t('custom_feild.lbl_show_table_view') }} </label>
                    <div class="form-check">
                      <input class="form-check-input" :value="1" name="is_view" id="is_view" type="checkbox" v-model="is_view" />
                    </div>
                  </div>
                </div>

                <div class="form-group" v-if="type === 'select' || type === 'radio' || type === 'checkbox'">
                  <div v-for="(input, index) in inputFields" :key="index" class="mb-3 mt-3">
                    <label class="form-label" for="label">Value</label>
                    <div class="d-flex">
                      <input type="text" class="form-control" v-model="input.value" />
                      <a v-if="inputFields.length > 1" class="btn btn-primary ml-2" @click="deleteInputField(index)">Delete</a>
                    </div>
                  </div>
                  <div class="mb-4">
                    <a @click="addInputField" class="text-primary" style="cursor: pointer;">
                      <h6><i class="fa fa-plus-circle" aria-hidden="true"></i> Add More</h6>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="d-grid d-md-flex gap-3 p-3">
              <button class="btn btn-primary d-block" :disabled="isSubmitting">
                <i class="fa-solid fa-floppy-disk"></i>
                {{$t('messages.save')}}
              </button>
              <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="modal">
                {{$t('messages.cancel')}}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { DATATABLE_URL, EDIT_URL, STORE_URL, UPDATE_URL } from '@/vue/constants/custom_field'
import { useField, useForm } from 'vee-validate'
import { useRequest, useOnModalHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { buildMultiSelectObject } from '@/helpers/utilities'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

useOnModalHide('exampleModal', () => setFormData(defaultData()))



const emit = defineEmits(['onSubmit'])

// props
const props = defineProps({
  id: { type: Number, default: 0 }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const currentId = ref(props.id)
const isSubmitting = ref(false); 
// Edit Form Or Create Form
watch(
  () => props.id,
  (value) => {
    currentId.value = value
    if (value > 0) {
      getRequest({ url: EDIT_URL, id: value }).then((res) => {
        if (res.status && res.data) {

          inputFields.value=res.data.inputFields
          setFormData(res.data)
        }
      })
    } else {
      setFormData(defaultData())
    }
  }
)

const getModuleList = () => {
  listingRequest({ url: DATATABLE_URL, data: { id: 0 } }).then((res) => (module_data.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })))
}

onMounted(() => {
  setFormData(defaultData())
  getModuleList()
  addInputField()

})

const module_data = ref({
  searchable: true,
  options: [],
  closeOnSelect: true
})

const type_data = ref({
  searchable: true,
  options: [
    { label: 'Text', value: 'text' },
    { label: 'Number', value: 'number' },
    { label: 'Password', value: 'password' },
    { label: 'Text Area', value: 'textarea' },
    { label: 'Select', value: 'select' },
    { label: 'Radio', value: 'radio' },
    { label: 'Date', value: 'date' },
    { label: 'Check Box', value: 'checkbox' },
    { label: 'Files', value: 'file' }
  ],
  closeOnSelect: true
})

const inputFields = ref([])

const addInputField = () => {
  inputFields.value.push({ value: '' })
}

const deleteInputField = (index) => {
  inputFields.value.splice(index, 1)
}

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    Module: '',
    label: '',
    type: '',
    required: '',
    is_export: '',
    is_view: '',
    inputFields: []
  }
}

//  Reset Form
const setFormData = (data) => {

  inputFields.value = [];

  if (data.inputFields && Array.isArray(data.inputFields)) {
    inputFields.value = data.inputFields.map(field => ({
      value: field.value || '' 
    }));
  }
  resetForm({
    values: {
      Module: data.custom_field_group_id,
      label: data.label,
      type: data.type,
      required: data.required ? true : false,
      is_export: data.is_export ? true : false,
      is_view: data.is_view ? true : false,

    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    // renderedDataTable.ajax.reload(null, false)
    bootstrap.Modal.getInstance('#exampleModal').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
  emit('onSubmit')
}

// Validations
const validationSchema = yup.object({
  Module: yup.number().required(),
  label: yup.string().required("Label is a required field"),
  type: yup.string().required("Type is a required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: Module } = useField('Module')
const { value: label } = useField('label')
const { value: type } = useField('type')
const { value: required } = useField('required')
const { value: is_export } = useField('is_export')
const { value: is_view } = useField('is_view')

const errorMessages = ref({})

// Form Submit
const formSubmit = handleSubmit((values) => {
  isSubmitting.value = true; 

  values.inputFields = JSON.stringify(inputFields.value)

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => {
      reset_datatable_close_offcanvas(res);
    }).finally(() => {
      isSubmitting.value = false; 
    });
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => {
      reset_datatable_close_offcanvas(res);
    }).finally(() => {
      isSubmitting.value = false; 
    });
  }
});
</script>
