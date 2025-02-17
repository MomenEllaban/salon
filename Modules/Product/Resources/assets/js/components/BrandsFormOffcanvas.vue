<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <div class="form-group">
              <div class="col-md-12 text-center">
                <img :src="ImageViewer || defaultImage" alt="feature-image" class="img-fluid mb-2 avatar-140 avatar-rounded" />
                <div v-if="validationMessage" class="text-danger mb-2">{{ validationMessage }}</div>
                <div class="d-flex align-items-center justify-content-center gap-2">
              <input type="file" ref="profileInputRef" class="form-control d-none" id="feature_image" name="feature_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
              <label class="btn btn-info" for="feature_image">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()"
                v-if="ImageViewer" />
              </div>
              <!-- <span v-if="errorMessages['feature_image']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['feature_image']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.feature_image }}</span> -->
            </div>
          </div>
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('brand.name')" placeholder="" v-model="name" :error-message="errors['name']" ></InputField>
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
  import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/brand'
  import { useField, useForm } from 'vee-validate'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
  import * as yup from 'yup'
  import { readFile } from '@/helpers/utilities'
  import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
  import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
  // import FormElement from '@/helpers/custom-field/FormElement.vue'

  // props
  const props = defineProps({
    createTitle: { type: String, default: '' },
    editTitle: { type: String, default: '' },
    defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
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

  // File Upload Function
  const ImageViewer = ref(null)
  // const editimg = ref(null);
  const profileInputRef = ref(null)
  const fileUpload = async (e) => {
  let file = e.target.files[0];
  const maxSizeInMB = 2;
  const maxSizeInBytes = maxSizeInMB * 1024 * 1024;

  if (file) {
    if (file.size > maxSizeInBytes) {
      // File is too large
      validationMessage.value = `File size exceeds ${maxSizeInMB} MB. Please upload a smaller file.`;
      // Clear the file input
      profileInputRef.value.value = '';
      return;
    }

    await readFile(file, (fileB64) => {
      ImageViewer.value = fileB64;
      profileInputRef.value.value = '';
      validationMessage.value = ''; 
    });
    feature_image.value = file;
  } else {
    validationMessage.value = '';
  }
};

  // Function to delete Images
  const removeImage = ({ imageViewerBS64, changeFile }) => {
    // ImageViewer.value = null
    // feature_image.value = null
    // refInput.value = ''
    // document.getElementById('feature_image').value = ''
    imageViewerBS64.value = null
    changeFile.value = null
  }

  const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })
  /*
   * Form Data & Validation & Handeling
   */
  // Default FORM DATA
  const defaultData = () => {
    errorMessages.value = {}
    return {
      name: '',
      status: true
    }
  }

  //  Reset Form
  const setFormData = (data) => {
    if(data.feature_image === props.defaultImage) {
      ImageViewer.value = null;
    }
    else {
      ImageViewer.value = data.feature_image;
    }
    resetForm({
      values: {
        name: data.name,
        status: data.status,
        feature_image: data.feature_image !== props.defaultImage ? data.feature_image : undefined
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
      // removeImage()
    } else {
      window.errorSnackbar(res.message)
      errorMessages.value = res.all_message
    }
  }

  // Validations
  const validationSchema = yup.object({
    name: yup.string().required('Name is a required field')
  })


  const { handleSubmit, errors, resetForm } = useForm({
    validationSchema
  })
  const { value: name } = useField('name')
  const { value: status } = useField('status')
  const { value: feature_image } = useField('feature_image')

  const errorMessages = ref({})
  const validationMessage = ref('');

  onMounted(() => {
    setFormData(defaultData())
  })

  const IS_SUBMITED = ref(false)
  const formSubmit = handleSubmit((values) => {
    if(IS_SUBMITED.value) return false

    IS_SUBMITED.value = true
    values.slug = values.name;
    // values.custom_fields_data = JSON.stringify(values.custom_fields_data);
    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' })
        .then((res) => reset_datatable_close_offcanvas(res));
    } else {
      storeRequest({ url: STORE_URL, body: values, type: 'file' })
        .then((res) => reset_datatable_close_offcanvas(res));
    }
  });

  </script>
