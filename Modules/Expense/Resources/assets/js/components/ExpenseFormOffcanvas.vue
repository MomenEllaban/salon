<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <InputField class="col-md-12" type="number"  :is-required="true" :label="$t('expense.amount')" :placeholder="$t('expense.enter_amount')"  v-model="amount" :error-message="errors.amount" :error-messages="errorMessages['amount']"></InputField>
              <InputField class="col-md-12" :is-required="true" :label="$t('expense.reference_number')" :placeholder="$t('expense.enter_reference_number')" v-model="reference_number" :error-message="errors.reference_number" :error-messages="errorMessages['reference_number']"></InputField>
              <label class="form-label" for="date">{{ $t('expense.date') }}</label>
                <div class="w-100">
                  <flat-pickr id="date"  class="form-control" :config="config" v-model="date" :value="date" placeholder="YYYY-MM-DD"></flat-pickr>
                </div>
              <InputField class="col-md-12" type="textarea" :label="$t('expense.note')" :placeholder="$t('expense.enter_note')" v-model="note" :error-message="errors.note" :error-messages="errorMessages['note']"></InputField>
              <div class="form-group col-md-12">
                <div class="form-group col-md-12">
                  <div class="d-flex justify-content-between">
                    <label for="branch_id">{{ $t('expense.branch') }} <span class="text-danger">*</span></label>
                  </div>
                  <Multiselect v-model="branch_id" :value="branch_id" :placeholder="$t('branch.branch')" :options="branch.options" v-bind="singleSelectOption" id="branch_id"></Multiselect>
                  <span v-if="errorMessages['branch_id']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['branch_id']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.branch_id }}</span>
                </div>

                <div class="form-group col-md-12">
                  <div class="d-flex justify-content-between">
                    <label for="manager_id">{{ $t('expense.expense_for') }} <span class="text-danger">*</span></label>
                  </div>
                  <Multiselect v-model="manager_id" :value="manager_id" :placeholder="$t('branch.assign_manager')" :options="manager.options" v-bind="singleSelectOption" id="manager_id"></Multiselect>
                  <span v-if="errorMessages['manager_id']">
                    <ul class="text-danger">
                      <li v-for="err in errorMessages['manager_id']" :key="err">{{ err }}</li>
                    </ul>
                  </span>
                  <span class="text-danger">{{ errors.manager_id }}</span>
                </div>

                <div class="form-group col-md-12">
                  <label for="parent-category">{{ $t('expense.expense_category') }} <span class="text-danger">*</span></label>
                    <Multiselect 
                      v-bind="singleSelectOption" 
                      v-model="category_id" 
                      :value="category_id"  
                      :placeholder="$t('category.placeholder_parent_category')" 
                      :options="categories.options"
                      @select="getSubcategories" ></Multiselect>
                  <span v-if="errors.category_id" class="text-danger">{{ errors.parent_id }}</span>
                </div>
                <!-- Subcategory Dropdown (Shown Only if Category is Selected) -->
                <div class="form-group col-md-12">
                  <label for="subcategory">{{ $t('expense.expense_subcategory') }}</label>
                  <Multiselect 
                    v-bind="singleSelectOption" 
                    v-model="subcategory_id" 
                    :options="subcategories.options" 
                    :placeholder="$t('expense.placeholder_sub_category')">
                  </Multiselect>
                  <span v-if="errors.subcategory_id" class="text-danger">{{ errors.subcategory_id }}</span>
                </div>
                <div class="text-center">
                  <img :src="ImageViewer || defaultImage" alt="feature-image" class="img-fluid mb-2 product-image-thumbnail" />
                  <div v-if="validationMessage" class="text-danger mb-2">{{ validationMessage }}</div>
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <input type="file" ref="profileInputRef" class="form-control d-none" id="feature_image" name="feature_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
                    <label class="btn btn-info" for="feature_image">{{ $t('messages.upload') }}</label>
                    <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()" v-if="ImageViewer" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 my-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('expense.lbl_status') }}</label>
                <div class="form-check form-switch m-2">
                  <input class="form-check-input" :value="status" :true-value="1" :false-value="0" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
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
import { EDIT_URL, STORE_URL, UPDATE_URL, EMPLOYEE_LIST, CATEGORY_LIST, SUBCATEGORY_LIST, BRANCH_LIST} from '../constant/expense'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FlatPickr from 'vue-flatpickr-component'
import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

const validationMessage = ref('');

const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png'}
})

// flatpicker
const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  minDate: new Date(),
  allowInput: true,
})

//Image file
const ImageViewer = ref(null)
const profileInputRef = ref(null)
// const fileUpload = async (e) => {
//   let file = e.target.files[0];
//   const allowedTypes = ['image/jpeg', 'image/png'];
//   const maxSizeInMB = 2;
//   const maxSizeInBytes = maxSizeInMB * 1024 * 1024;

//   if (!allowedTypes.includes(file.type)) {
//     window.errorSnackbar('Only JPG, JPEG, and PNG files are allowed.'); 
//     profileInputRef.value.value = ''; 
//     return;
//   }

//   if (file.size > maxSizeInBytes) {
//     validationMessage.value = `File size exceeds ${maxSizeInMB} MB. Please upload a smaller file.`;
//     profileInputRef.value.value = '';
//     return;
//   }

//   await readFile(file, (fileB64) => {
//     ImageViewer.value = fileB64;
//     profileInputRef.value.value = '';
//     validationMessage.value = ''; 
//   });
//   feature_image.value = file;
// };

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

const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })

const { getRequest, storeRequest, updateRequest } = useRequest()

onMounted(() => {
  setFormData(defaultData())
  getBranchList()
  getManagerList()
  getCategories()
})

const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
        getBranchList(() => branch_id.value = res.data.branch_id)
        getManagerList(() => manager_id.value = res.data.manager_id)
        getCategories(() => category_id.value = res.data.category_id)
        getSubcategories(() => subcategory_id.value = res.data.subcategory_id)
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const defaultData = () => {
  errorMessages.value = {}
  return {
    title: '',
    amount: '',
    reference_number: '',
    date: '',
    note: '',
    branch_id: '',
    manager_id: '',
    category_id: '',
    subcategory_id: '',
    feature_image: null,
    status: true
  }
}

const setFormData = (data) => {
  if (data.feature_image === props.defaultImage) {
    ImageViewer.value = null
  } else {
    ImageViewer.value = data.feature_image
  }
  resetForm({
    values: {
      title: data.title,
      amount: data.amount,
      reference_number: data.reference_number,
      date: data.date,
      note: data.note,
      status: data.status,
      branch_id: data.branch_id,
      manager_id: data.manager_id,
      category_id: data.category_id,
      subcategory_id: data.subcategory_id,
      feature_image: data.feature_image !== props.defaultImage ? data.feature_image : undefined,

    }
  })
}

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
const validationSchema = yup.object({
  amount: yup.number().required('Amount is a required field').typeError('Amount must be a number').positive('Amount must be greater than 0'),
  reference_number: yup.string().required('Reference number is required'),
  date: yup.date().typeError('Invalid date format'),
  note: yup.string().nullable(),
  branch_id: yup.number().required('Assign Expense For Branch is a required field'),
  manager_id: yup.number().required('Assign Expense For Manager is a required field'),
  category_id: yup.number().required('Expense Category is a required field'),
  subcategory_id: yup.string().when('category_id', {
    is: (val) => !!val,
    then: (schema) => schema.required('Subcategory is required'),
  })
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: amount } = useField('amount')
const { value: reference_number } = useField('reference_number')
const { value: date } = useField('date')
const { value: note } = useField('note')
const { value: status } = useField('status')
const { value: branch_id } = useField('branch_id')
const { value: manager_id } = useField('manager_id')
const { value: category_id } = useField('category_id')
const { value: subcategory_id } = useField('subcategory_id')
const { value: feature_image } = useField('feature_image')


const branch = ref({ options: [], list: [] })
const manager = ref({ options: [], list: [] })

const getBranchList = (cb = null) => {
  useSelect({ url: BRANCH_LIST}, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
      if(typeof cb == 'function') {
        cb()
      }
    }
  )
}

const getManagerList = (cb = null) => {
  useSelect({ url: EMPLOYEE_LIST, data: { role: 'manager' } }, { value: 'id', label: 'name' }).then((data) => {
      manager.value = data
      if(typeof cb == 'function') {
        cb()
      }
    }
  )
}

const categories = ref({ options: [], list: [] })
const getCategories = (cb = null) => {
  useSelect({ url: CATEGORY_LIST }, { value: 'id', label: 'name' }).then((data) => {
    categories.value = data
      if(typeof cb == 'function') {
        cb()
      }
    }
  )
}

const subcategories = ref({ options: [], list: [] })
// Fetch subcategories based on selected category
const getSubcategories = (cb = null) => {
  if (!category_id.value) return;

  const parentId = category_id.value.id || category_id.value;

  useSelect({ url: SUBCATEGORY_LIST, data: { parent_id: parentId } }, { value: 'id', label: 'name' })
    .then((data) => {
      subcategories.value = data;
      if (typeof cb === 'function') {
        cb();
      }
    });
};

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const errorMessages = ref({})
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if (IS_SUBMITED.value) return false
  IS_SUBMITED.value = true

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>

<style>
.multiselect-clear {
    display: none !important;
}
</style>