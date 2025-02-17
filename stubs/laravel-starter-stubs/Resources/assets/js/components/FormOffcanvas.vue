<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('Name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>

          <!-- <div class="form-group">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label" for="feature_image">{{ $t('service.lbl_feature_image') }} </label>
              <a href="javascript:void(0)" v-if="feature_image" class="text-danger" @click="removeImage">Remove</a>
            </div>
            <input type="file" class="form-control" id="feature_image" ref="refInput" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
            <span v-if="errorMessages['feature_image']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['feature_image']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors.feature_image }}</span>
          </div> -->

          <div v-for="field in customefield" :key="field.id">
            <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
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
  import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant'
  import { useField, useForm } from 'vee-validate'
  import InputField from '@/vue/components/form-elements/InputField.vue'

  import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import { readFile } from '@/helpers/utilities'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
  import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
  import FormElement from '@/helpers/custom-field/FormElement.vue'

  // props
  defineProps({
    createTitle: { type: String, default: '' },
    editTitle: { type: String, default: '' },
    customefield: { type: Array, default: () => [] }
  })

  const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

  // Edit Form Or Create Form
  const currentId = useModuleId(() => {
    subCategories.value.options = []
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

  // File Upload Function
  const ImageViewer = ref(null)
  const refInput = ref(null)
  const fileUpload = async (e) => {
    let file = e.target.files[0]
    await readFile(file, (fileB64) => {
      ImageViewer.value = fileB64
    })
    feature_image.value = file
  }

  // Function to delete Images
  const removeImage = () => {
    ImageViewer.value = null
    feature_image.value = null
    refInput.value = ''
    document.getElementById('feature_image').value = ''
  }

  /*
   * Form Data & Validation & Handeling
   */
  // Default FORM DATA
  const defaultData = () => {
    errorMessages.value = {}
    return {
      name: '',
    }
  }

  //  Reset Form
  const setFormData = (data) => {
    ImageViewer.value = data.feature_image
    resetForm({
      values: {
        name: data.name,
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

  const errorMessages = ref({})

  onMounted(() => {
    setFormData(defaultData())
  })

  // Form Submit
  const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false
    values.custom_fields_data = JSON.stringify(values.custom_fields_data);
    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' })
        .then((res) => reset_datatable_close_offcanvas(res))
    } else {
      storeRequest({ url: STORE_URL, body: values, type: 'file' })
        .then((res) => reset_datatable_close_offcanvas(res))
    }
  });

  </script>
