<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
              <div class="text-center">
                <img :src="ImageViewer || defaultImage" alt="feature-image" class="img-fluid mb-2 avatar-140 avatar-rounded" />
                <div v-if="validationMessage" class="text-danger mb-2">{{ validationMessage }}</div>
                <div class="d-flex align-items-center justify-content-center gap-2">
                  <input type="file" ref="profileInputRef" class="form-control d-none" id="feature_image" name="feature_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
                  <label class="btn btn-info" for="feature_image">{{ $t('messages.upload') }}</label>
                  <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()" v-if="ImageViewer" />
                </div>
              </div>
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('slider.lbl_name')" :placeholder="$t('slider.enter_name')" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>
        <InputField class="col-md-12" type="text" :is-required="false" :label="$t('slider.lbl_link')" :placeholder="$t('slider.enter_url')" v-model="link"></InputField>

        <div class="col-12">
          <div class="form-group">
            <label class="form-label">{{ $t('slider.lbl_type') }}<span class="text-danger">*</span></label>
            <Multiselect id="type" v-model="type" :value="type" v-bind="singleSelectOption" :placeholder="$t('slider.select_type')" :options="module_types.options" @select="linkSelect" class="form-group"></Multiselect>
            <span class="text-danger">{{ errors.type }}</span>
          </div>
        </div>


        <div class="col-12">
          <div class="form-group">
            <label class="form-label">{{ $t('slider.lbl_link_id') }}<span class="text-danger">*</span></label>
            <Multiselect id="link_id" v-model="link_id" :value="link_id" v-bind="singleSelectOption" :options="modules_data.options" class="form-group"></Multiselect>
            <span class="text-danger">{{ errors.link_id }}</span>
          </div>
        </div>

        <div class="form-group d-none">
          <div class="d-flex justify-content-between align-items-center">
            <label class="form-label" for="slider-status">{{ $t('slider.lbl_status') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :value="status" :checked="status" name="status" id="slider-status" type="checkbox" v-model="status" />
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
import { CATEGORY_LIST, SERVICE_LIST, TYPE_LIST, STORE_URL, EDIT_URL, UPDATE_URL ,SLIDERS_UNIQUE_CHECK} from '../constant/slider'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest,useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { readFile } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import { useSelect } from '@/helpers/hooks/useSelect'

// props
const props =defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const ImageViewer = ref(null)
// const editimg = ref(null);
const profileInputRef = ref(null)
const validationMessage = ref('');

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
  imageViewerBS64.value = null
  changeFile.value = null
}

const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })



const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true,
  clearable: false
})

const module_types = ref({ options: [], list: [] })
const modules_data = ref({ options: [], list: [] })
const errorMessages = ref({})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

onMounted(() => {
  useSelect({ url: TYPE_LIST }, { value: 'id', label: 'name' }).then((data) => (module_types.value = data))
  setFormData(defaultData())
})

const getList = () => {
  const typeId = type.value
  if (typeId === 'category') {
    getCategoryLIst()
  } else if (typeId === 'service') {
    getServiceList()
  }
}

const getServiceList = () => {
  useSelect({ url: SERVICE_LIST, data: { type: type.value } }, { value: 'id', label: 'name' }).then((data) => (modules_data.value = data))
}

const getCategoryLIst = () => {
  useSelect({ url: CATEGORY_LIST, data: { type: type.value } }, { value: 'id', label: 'name' }).then((data) => (modules_data.value = data))
}

const linkSelect = () => {
  getList()
}
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if(res.status) {
        console.log(res.data)
        setFormData(res.data)
        linkSelect()
      }
    })
  } else {
    setFormData(defaultData())
  }
})


// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  ImageViewer.value = props.defaultImage
  return {
    name: '',
    type: '',
    link: '',
    link_id: '',
    status: 1,
    feature_image: null
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
      type: data.type,
      link: data.link,
      link_id: data.link_id,
      status: data.status,
      feature_image: data.feature_image !== props.defaultImage ? data.feature_image : undefined,
    }
  })
}

const numberRegex = /^\d+$/
const validationSchema = yup.object({
  name: yup
    .string()
    .required('Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)) 
    .test('unique', 'Name must be unique', async function(value) {
      const sliderId = currentId.value; 
      const isUnique = await storeRequest({url: SLIDERS_UNIQUE_CHECK,body: { name: value, id: sliderId }, type: 'file'});
      if (!isUnique.isUnique) {
        return this.createError({
          path: 'name',
          message: 'Name must be unique'
        });
      }
      return true;
    }),
  type: yup.string().required('Type is a required field'),
  link_id: yup.string().required("Link Id is a required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: link } = useField('link')
const { value: type } = useField('type')
const { value: link_id } = useField('link_id')
const { value: status } = useField('status')
const { value: feature_image } = useField('feature_image')

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false

  // image required bug solved
  if (!ImageViewer.value || ImageViewer.value === props.defaultImage) {
    validationMessage.value = 'Banner image is required.';
    return; // Stop form submission
  }

  IS_SUBMITED.value = true
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

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

</script>
