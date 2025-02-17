<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <fieldset>
          <legend>{{ $t('product.product_information') }}</legend>
          <div class="row">
            <div class="form-group col-md-4">
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

            <div class="col-md-8">
              <InputField class="" type="text" :is-required="true" :label="$t('product.name')" placeholder="" v-model="name" :error-message="errors['name']"></InputField>
              <InputField class="" type="textarea" :textareaRows="5" :label="$t('product.description')" placeholder="" v-model="short_description"></InputField>
            </div>

            <div class="col-md-12 form-group editor-container">
              <label class="form-label" for="description">{{ $t('product.long_description') }}</label>
              <!-- Add Quill editor here -->
              <QuillEditor theme="snow" v-model:content="description" contentType="html"/>
              <span class="text-danger">{{ errors.description }}</span>
            </div>

            <div class="form-group col-md-6">
              <label class="form-label">{{ $t('product.brand') }} <span class="text-danger">*</span></label>
              <Multiselect id="brand-list" v-model="brand_id" :value="brand_id" placeholder="Select Brand" v-bind="singleSelectOption" :options="brands.options" @select="selectBrand" class="form-group"></Multiselect>
              <span v-if="errorMessages['brand_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['brand_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.brand_id }}</span>               </div>

            <div class="form-group col-md-6">
              <label class="form-label" for="categories">{{ $t('product.categories') }} <span class="text-danger">*</span></label>
              <Multiselect id="categories" v-model="category_ids" :value="category_ids" placeholder="Select Category" v-bind="multiselectOption" :options="category.options" class="form-group"></Multiselect>
              <span class="text-danger">{{ errors['category_ids'] }}</span>
            </div>

            <div class="form-group col-md-6">
              <label class="form-label">{{ $t('product.tags') }}</label>
              <Multiselect v-model="tags" :value="tags" v-bind="multiselectCreateOption" :options="tagsList.options" id="tags-list" autocomplete="off"></Multiselect>
            </div>

            <div class="col-md-6 form-group">
              <label class="form-label">{{ $t('product.unit') }}</label>
              <Multiselect id="units-list" v-model="unit_id" :value="unit_id" v-bind="singleSelectOption" :options="units.options" class="form-group"></Multiselect>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>{{ $t('product.product_price') }}</legend>
          <div class="form-group">
            <div class="d-flex justify-content-end">
              <label class="form-label me-2" for="category-status">{{ $t('product.has_variation') }}</label>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" :value="has_variation" :true-value="1" :false-value="0" :checked="has_variation" v-model="has_variation" />
              </div>
            </div>
          </div>
          <div class="row" v-if="has_variation">
            <template v-for="(varData, index) in variations" :key="index">
              <div class="col-md-12">
                <div class="d-flex gap-3 align-items-center">
                  <div class="d-flex flex-grow-1 gap-3">
                    <div class="form-group w-50">
          <label for="">{{ $t('product.variation_type') }}</label>
                      <Multiselect id="variations-list" v-model="varData.value.variation" :value="varData.value.variation" v-bind="singleSelectOption" @select="generateCombinations" @deselect="generateCombinations" :options="variationsData.options.filter((item) => item.id !== varData.value.variation)" @change="() => varData.value.variationValue = []" class="form-group"></Multiselect>

                    </div>
                    <div class="form-group w-50">
                      <label for="">{{ $t('product.variation_value') }}</label>
                      <Multiselect id="variations-list" v-model="varData.value.variationValue" @select="generateCombinations" @deselect="generateCombinations" :value="varData.value.variationValue" v-bind="multiselectOption" :options="variationValueCheck(varData.value.variation)" class="form-group"></Multiselect>
                    </div>
                  </div>
                  <div v-if="variations.length > 1">
                    <button
                      class="btn btn-danger btn-icon"
                      @click="variationsSplice(index);generateCombinations()">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </template>
            <div class="col-md-6" v-if="variations.length !== variationsData.options.length">
              <div class="form-group">
                <button class="btn btn-secondary" type="button" @click="variationsPush({ variation: '', variationValue: [] })">+ {{ $t('product.add_more_variation') }}</button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{{ $t('product.variation') }}</th>
                      <th>{{ $t('product.price_tax') }}</th>
                      <th>{{ $t('product.stock') }}</th>
                      <th>{{ $t('product.sku') }}</th>
                      <th>{{ $t('product.code') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(comb, index) in combinations" :key="index">
                      <td><input class="form-control" v-model="comb.value.variation" :readonly="true" :disabled="true" /></td>
                      <td><input class="form-control" v-model="comb.value.price" type="number" :min="0.01" :step="0.01" /></td>
                      <td><input class="form-control" v-model="comb.value.stock" type="number" :min="1" :step="1" /></td>
                      <td><input class="form-control" v-model="comb.value.sku" /></td>
                      <td><input class="form-control" v-model="comb.value.code" /></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row" v-else>
            <InputField class="col-md-3" type="number"  :is-required="true" :error-messages="errorMessages['price']" :step="0.01" :min="0.01" :label="$t('product.price_tax')" placeholder="" v-model="price" :error-message="errors['price']"></InputField>
            <InputField class="col-md-3" type="number" :is-required="true" :error-messages="errorMessages['stock']" :step="0" :min="1" :label="$t('product.stock')" placeholder="" v-model="stock" :error-message="errors['stock']"></InputField>
            <InputField class="col-md-3" type="text" :label="$t('product.sku')" placeholder="" v-model="sku" :error-message="errors['sku']"></InputField>
            <InputField class="col-md-3" type="text" :label="$t('product.code')" placeholder="" v-model="code" :error-message="errors['code']"></InputField>
          </div>
        </fieldset>
        <fieldset>
          <legend>{{ $t('product.product_discount') }}</legend>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="form-label" for="date">{{ $t('product.date') }}</label>
                <div class="w-100">
                  <flat-pickr id="date" class="form-control" :config="config" v-model="date_range" :value="date_range"></flat-pickr>
                </div>
              </div>
            </div>
            <InputField class="col-md-4" type="text" :label="$t('product.amount')" placeholder="" v-model="discount_value" :error-message="errors['discount_value']"></InputField>
            <div class="col-md-4">
              <label class="form-label">{{ $t('product.percent_or_fixed') }}</label>
              <Multiselect v-model="discount_type" :value="discount_type" v-bind="singleSelectOption" :options="typeOptions" id="type" autocomplete="off"></Multiselect>
            </div>
          </div>
        </fieldset>
        <div class="row">
          <div class="col-md-12 px-5">
            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                  <input class="form-check-input m-0" :value="is_featured" :checked="is_featured" :true-value="1" :false-value="0" name="is_featured" id="is_featured" type="checkbox" v-model="is_featured" />
                  <label class="form-label m-0" for="is_featured">{{ $t('product.lbl_is_featured') }}</label>
                </div>
                <div class="d-flex align-items-center gap-3">
                  <label class="form-label" for="status">{{ $t('product.lbl_status') }}</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" :value="status" :checked="status" :true-value="1" :false-value="0" name="status" id="status" type="checkbox" v-model="status" />
                  </div>
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
import { EDIT_URL, STORE_URL, UPDATE_URL } from '../constant/product'
import { CATEGORY_LIST, BRAND_LIST, UNITS_LIST, TAGS_LIST, VARIATIONS_LIST } from '../constant/product'
import { useField, useForm, useFieldArray } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FlatPickr from 'vue-flatpickr-component'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import { buildMultiSelectObject } from '@/helpers/utilities'

// ... (rest of the setup code) ...

const descriptionEditorRef = ref(null);
const validationMessage = ref('');

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

// flatpicker
const config = ref({
  dateFormat: 'Y-m-d',
  static: true,
  mode: 'range'
})

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  // if(data.value.brand_id) {
  //     brand_id.value = data.value.brand_id
  //     selectBrand(brand_id.value)
  //   }
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
      }
    })
  } else {
    setFormData(defaultData())
    variationsPush({ variation: '', variationValue: [] })
    generateCombinations()
  }
})

useOnOffcanvasHide('form-offcanvas', () => {
  setFormData(defaultData())
  console.log(descriptionEditorRef.value)
})

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
    slug: '',
    status: 1,
    short_description: '',
    description: ' ',
    category_ids: [],
    tags: [],
    brand_id: '',
    unit_id: '',
    price: '',
    stock: '',
    sku: '',
    code: '',
    discount_value: 0,
    discount_type: 'percent',
    date_range: null,
    variations: [],
    combinations: [],
    has_variation: 0,
    feature_image: null,
    is_featured: 0
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.feature_image
  resetForm({
    values: {
      name: data.name,
      slug: data.slug,
      short_description: data.short_description || '',
      description: data.description || '',
      category_ids: data.category_ids || [],
      tags: data.tags || [],
      brand_id: data.brand_id || null,
      unit_id: data.unit_id || null,
      price: data.price || 0,
      stock: data.stock || 0,
      sku: data.sku || null,
      code: data.code || null,
      discount_value: data.discount_value || 0,
      discount_type: data.discount_type || 'percent',
      date_range: data.date_range || null,
      variations: data.variations || {},
      combinations: data.combinations || [],
      has_variation: data.has_variation || 0,
      feature_image: data.feature_image,
      status: data.status || 1,
      is_featured: data.is_featured || 0,
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
    removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Validations
const validationSchema = yup.object({
  name: yup.string().required(' Product Name is a required field').max(190, 'Product Name must be at most 190 characters'),
  brand_id: yup.string().required(' Brand is a required field'),

  category_ids: yup.array().test('category_ids', 'Category is a required field', function (value) {
    if (value.length == 0) {
      return false
    }
    return true
  }),
  brand_id: yup.string()
    .required('Select Brand is a required field'),
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: status } = useField('status')
const { value: is_featured } = useField('is_featured')
const { value: short_description } = useField('short_description')
const { value: description } = useField('description')
const { value: category_ids } = useField('category_ids')
const { value: brand_id } = useField('brand_id')
const { value: tags } = useField('tags')
const { value: unit_id } = useField('unit_id')
const { value: price } = useField('price')
const { value: stock } = useField('stock')
const { value: sku } = useField('sku')
const { value: code } = useField('code')
const { value: discount_value } = useField('discount_value')
const { value: discount_type } = useField('discount_type')
const { value: date_range } = useField('date_range')
const { value: has_variation } = useField('has_variation')
const { fields: variations, push: variationsPush, remove: variationsSplice } = useFieldArray('variations')
const { fields: combinations, push: combinationsPush, remove: combinationsSplice, replace: combinationsReplace } = useFieldArray('combinations')
const { value: feature_image } = useField('feature_image')


const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
  getBrand()
  getCategory()
  getUnits()
  getTags()
  getVariations()
})


const brands = ref({ options: [], list: [] })

const getBrand = () => useSelect({ url: BRAND_LIST }, { value: 'id', label: 'name' }).then((data) => (brands.value = data))

const selectBrand = (value) => {
  getCategory(value)
}

const category = ref({ options: [], list: [] })

const getCategory = (value) => useSelect({ url: CATEGORY_LIST, data: {brand_id: value}}, { value: 'id', label: 'name' }).then((data) => (category.value = data))

const units = ref({ options: [], list: [] })

const getUnits = () => useSelect({ url: UNITS_LIST }, { value: 'id', label: 'name' }).then((data) => (units.value = data))

const tagsList = ref({ options: [], list: [] })

const getTags = () => useSelect({ url: TAGS_LIST }, { value: 'name', label: 'name' }).then((data) => (tagsList.value = data))

const variationsData = ref({ options: [], list: [] })

const getVariations = () => useSelect({ url: VARIATIONS_LIST }, { value: 'id', label: 'name' }).then((data) => (variationsData.value = data))

const variationValueCheck = (data) => {
  return buildMultiSelectObject(variationsData.value.list.find((item) => item.id == data)?.values || [], { value: 'id', label: 'name' })
}
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true
  values.combinations = JSON.stringify(values.combinations)
  values.tags = JSON.stringify(values.tags)
  values.category_ids = JSON.stringify(values.category_ids)

  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

const generateCombinations = async () => {
  const varVal = variations.value
  const valuesArray = varVal.map((item) => (item.value.variation !== '' && item.value.variationValue.length > 0 ? item.value.variationValue : '')) || []
  const numVariations = varVal.filter((item) => item.value.variation !== '' && item.value.variationValue.length > 0).length

  const result = []
  const currentCombination = new Array(numVariations)
  const variationValuesArr = []
  const newVarval = variationsData.value.list
  newVarval.map((variation) => {
    const variationId = variation.id // Extract variation ID
    variation.values.forEach((value) => {
      value.variation_id = variationId // Add variation_id to each value object
    })
    variationValuesArr.push(...variation.values)
  })
  function backtrack(index) {
    if (index === numVariations) {
      const val_key = currentCombination
        .map((v) => {
          return variationValuesArr.find((x) => x.id == v).variation_id + ':' + v
        })
        .join('/')
      const val = currentCombination
        .map((v) => {
          return variationValuesArr.find((x) => x.id == v).name
        })
        .join('-')
      result.push({ variation_key: val_key, variation: val, price: 0, stock: 0, sku: val, code: val.toLowerCase() })
      return
    }
    for (const value of valuesArray[index]) {
      currentCombination[index] = value
      backtrack(index + 1)
    }
  }

  backtrack(0)

  await combinationsReplace([])
  result.map((comb) => {
    combinationsPush(comb)
  })

  return result
}

const typeOptions = [
  { label: 'Percent %', value: 'percent' },
  { label: 'Fixed', value: 'fixed' }
]
// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const multiselectOption = ref({
  mode: 'tags',
  searchable: true
})

const multiselectCreateOption = ref({
  mode: 'tags',
  createOption: true,
  searchable: true
})
</script>
<style scoped>
.product-image-thumbnail {
  width: 100%;
  object-fit: cover;
  height: 200px;
  max-height: 200px;
  border-radius: 1rem;
  padding: 0.75rem;
  border: 1px solid var(--bs-border-color);
}
@media only screen and (min-width: 768px) {
  .offcanvas {
    width: 80%;
  }
}

@media only screen and (min-width: 1280px) {
  .offcanvas {
    width: 60%;
  }
}
.editor-container {
    height: 200px;
}
</style>
