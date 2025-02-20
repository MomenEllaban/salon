<template>
  <form @submit="formSubmit">
          <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="form-offcanvasLabel">
            <template v-if="currentId != 0">
                <span v-if="parent_id !== null">{{editNestedTitle}}</span> <span v-else>{{editTitle}}</span>
            </template>
            <template v-else>
                <span v-if="parent_id !== null">{{createNestedTitle}}</span> <span v-else>{{createTitle}}</span>
            </template>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <InputField class="col-md-12" :is-required="true" :label="$t('expense.lbl_name')" 
                :placeholder="$t('expense.enter_name')" v-model="name" 
                :error-message="errors.name" :error-messages="errorMessages['name']">
              </InputField>
              <InputField class="col-md-12" :label="$t('expense.lbl_category_code')" 
                :placeholder="$t('expense.enter_category_code')" v-model="category_code" 
                :error-message="errors.category_code" :error-messages="errorMessages['category_code']">
              </InputField>
              <div v-if="isSubCategory" class="mt-3">
                <label for="parent-category">{{ $t('expense.lbl_parent_category') }}</label>
                <Multiselect v-bind="singleSelectOption" v-model="parent_id" :value="parent_id"  :placeholder="$t('category.placeholder_parent_category')" :options="categories"></Multiselect>
                <span v-if="errors.parent_id" class="text-danger">{{ errors.parent_id }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 my-3">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label" for="category-status">{{ $t('expense.lbl_status') }}</label>
              <div class="form-check form-switch m-2">
                <input class="form-check-input" :value="status" :true-value="1" :false-value="0" 
                  :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
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
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, INDEX_LIST_URL } from '../constant/category'
import { useField, useForm } from 'vee-validate'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import InputField from '@/vue/components/form-elements/InputField.vue'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'


const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  isSubCategory: { type: Boolean, default: false },
  createNestedTitle: { type: String, default: '' },
  editNestedTitle: { type: String, default: '' },
})

const { getRequest, storeRequest, updateRequest } = useRequest()

onMounted(() => {
  setFormData(defaultData())
    return { name: '', category_code: '', status: true, parent_id: '', feature_image: null }
})

const categories = ref([])
const category_name = ref(null)
const getCategories = () => {
  getRequest({ url: INDEX_LIST_URL }).then((res) => (categories.value = buildMultiSelectObject(res, { value: 'id', label: 'name' })))
}

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

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

const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    category_code: '',
    status: true
  }
}

const setFormData = (data) => {
  category_name.value = data.category_name

  resetForm({
    values: {
      name: data.name,
      category_code: data.category_code,
      status: data.status,
      parent_id: data.parent_id,

    }
  })
}

const updatecurrentId = (e) => {
  setFormData(defaultData())
  currentId.value = Number(e.detail.form_id)
  parent_id.value = e.detail.parent_id || null
  category_name.value = null
  if(props.isSubCategory) {
    getCategories()
    parent_id.value = -1
  }
}
watch(
  currentId,
  () => {
    if (currentId.value > 0) {
      getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => res.status && setFormData(res.data))
    } else {
      setFormData(defaultData())
    }
  },
  { deep: true }
)

onMounted(() => document.addEventListener('crud_change_id', updatecurrentId))
onUnmounted(() => document.removeEventListener('crud_change_id', updatecurrentId))

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
  name: yup.string().required('Name is a required field'),
  category_code: yup.string().nullable(),
  status: yup.boolean().required('Status is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: category_code } = useField('category_code')
const { value: status } = useField('status')
const { value: parent_id } = useField('parent_id')

const errorMessages = ref({})
const IS_SUBMITED = ref(false)

const formSubmit = handleSubmit((values) => {
  console.log("Form Submitted", values);

  if (IS_SUBMITED.value) return false
  IS_SUBMITED.value = true

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>

<style>
.multiselect-clear {
  display: none !important;
}
</style>