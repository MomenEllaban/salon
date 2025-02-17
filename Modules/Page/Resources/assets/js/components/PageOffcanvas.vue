<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">

            <InputField class="col-md-12" :is-required="true" :label="$t('page.lbl_title')"  :placeholder="$t('page.enter_title')" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>


            <div class="form-group">
              <label class="form-label" for="description">{{ $t('page.lbl_description') }}</label>
              <!-- Add Quill editor here -->
              <div ref="descriptionEditorRef" :placeholder="$t('page.enter_decsription')"></div>
              <span v-if="errorMessages['description']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.description }}</span>
            </div>


             <div class="form-group">
              <label class="form-label" for="sequence">{{ $t('page.lbl_sequence') }}</label>
              <input type="number" class="form-control" :placeholder="$t('page.select_sequence')" v-model="sequence" id="sequence" />
              <span v-if="errorMessages['sequence']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['sequence']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.sequence }}</span>
            </div>

             <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="page-status">{{ $t('page.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox"
                    v-model="status" />
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
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/page'
import { useField, useForm } from 'vee-validate'

import { useModuleId, useRequest,useOnOffcanvasHide} from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'

import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';

import Quill from 'quill'; // Import Quill


// ... (rest of the setup code) ...

const descriptionEditorRef = ref(null);

// Quill setup
let quillInstance;


// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

onMounted(() => {
  quillInstance = new Quill(descriptionEditorRef.value, {
    theme: 'snow', // Choose the theme. 'snow' provides a standard toolbar.
    // Add more configuration options here if needed.
  });
  // Bind the Quill editor content to the 'description' data property
  quillInstance.on('text-change', () => {
    description.value = quillInstance.root.innerHTML;
  });
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
    sequence: '',
    status: 1
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      description: data.description,
      sequence: data.sequence,
      status: data.status ? true : false,
    }
  });

  // Manually set Quill editor content for the description field
  if (quillInstance) {
    quillInstance.root.innerHTML = data.description || ''; // Set to empty if no description
  }
};

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
    .required('Title is a required field') ,

})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: description } = useField('description')
const { value: sequence } = useField('sequence')
const { value: status } = useField('status')
const errorMessages = ref({})


// Form Submit
const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true;
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

</script>
