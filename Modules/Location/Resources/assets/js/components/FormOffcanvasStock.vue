<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas-stock" aria-labelledby="form-offcanvas-stockLabel">
      <FormHeader :currentId="0" :editTitle="$t(editTitle)" :createTitle="$t(createTitle)"></FormHeader>
      <div class="offcanvas-body">
        <!-- <div class="form-group">
          <label class="form-label">Location</label>
          <Multiselect id="location_id-list" v-model="location_id" :value="location_id" v-bind="singleSelectOption" :options="LOCATIONS_OPTIONS" class="form-group" @select="getProductVariationsData"></Multiselect>
          <span class="text-danger">{{ errors['location_id'] }}</span>
          <ul class="m-0">
            <li class="text-danger" v-for="msg in errorMessages['location_id']" :key="msg">{{ msg }}</li>
          </ul>
        </div> -->
        <div class="form-group">
          <label class="form-label">{{ $t('product.brand') }} <span class="text-danger">*</span></label>
          <Multiselect id="brand-list" v-model="brand_id" :value="brand_id" placeholder="Select Brand" v-bind="singleSelectOption" :options="brands.options" @select="selectBrand" class="form-group"></Multiselect>
          <span class="text-danger">{{ errors['brand_id'] }}</span>
          <ul class="m-0">
            <li class="text-danger" v-for="msg in errorMessages['brand_id']" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <div class="form-group col-md-12">
          <label class="form-label" for="categories">{{ $t('product.categories') }} <span class="text-danger">*</span></label>
          <Multiselect id="categories" v-model="category_id" :value="category_id" placeholder="Select Category" v-bind="multiselectOption" :options="category.options" @select="selectCategory" class="form-group"></Multiselect>
          <span class="text-danger">{{ errors['category_ids'] }}</span>
          <ul class="m-0">
            <li class="text-danger" v-for="msg in errorMessages['category_ids']" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <div class="form-group">
          <label class="form-label">{{ $t('product.title') }}</label>
          <Multiselect id="product_id-list" v-model="product_id" :value="product_id" v-bind="singleSelectOption" placeholder="Select Product" :options="products.options" class="form-group" @select="getProductVariationsData"></Multiselect>
          <span class="text-danger">{{ errors['product_id'] }}</span>
          <ul class="m-0">
            <li class="text-danger" v-for="msg in errorMessages['product_id']" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <template v-if="product_id">
          <template v-if="has_variation">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <label for="" class="control-label">{{ $t('product.variation') }}</label>
                  </th>
                  <th data-breakpoints="xs sm">
                    <label for="" class="control-label">{{ $t('product.stock') }}</label>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr class="variant" v-for="(variation, index) in variations">
                  <td>
                    <input type="text" v-model="variation.value.name" class="form-control" disabled="" readonly>
                    <input type="hidden" name="variationsIds[]" v-model="variation.value.product_variation_id">
                  </td>
                  <td>
                    <input type="number" name="variationStocks[]" :min="1" class="form-control" required="" v-model="variation.value.stock">
                  </td>
                </tr>
              </tbody>
            </table>
          </template>
          <template v-else>
            <InputField  class="col-md-12" type="number" :min="1" :label="$t('product.stock')" placeholder="" v-model="stock" :error-message="errors['stock']" :error-messages="errorMessages['stock']"></InputField>
            <input type="hidden" v-model="product_variation_id">
          </template>
        </template>
      </div>
      <div class="offcanvas-footer border-top">
        <div class="d-grid d-md-flex gap-3 p-3">
          <button class="btn btn-primary d-block" :disabled="!product_id">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $t('messages.save') }}
          </button>
          <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas">
            <i class="fa-solid fa-angles-left"></i>
            {{ $t('messages.close') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { STOCK_STORE_URL, GET_PRODUCT_VARIATIONS_LIST, BRAND_LIST, CATEGORY_LIST, PRODUCT_LIST} from '../constant'
import { useField, useForm, useFieldArray } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'

// props
defineProps({
  createTitle: { type: String, default: "product.add_stock"},
  editTitle: { type: String, default: "product.add_stock"}
})

onMounted(() => {
  setFormData(defaultData())
  getBrand()
})

const data = useModuleId(() => {
    if(data.value.brand_id) {
      brand_id.value = data.value.brand_id
      selectBrand(brand_id.value)
    }
    if(data.value.category_id) {
      category_id.value = data.value.category_id
      selectCategory(category_id.value)
    }
    if(data.value.product_id) {
      product_id.value = data.value.product_id
      getProductVariationsData()
    }
    }, 'custom_form')

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

const brands = ref({ options: [], list: [] })

const getBrand = () => useSelect({ url: BRAND_LIST }, { value: 'id', label: 'name' }).then((data) => (brands.value = data))

const selectBrand = (value) => {
  getCategory(value)
}

const category = ref({ options: [], list: [] })

const getCategory = (value) => useSelect({ url: CATEGORY_LIST, data: {brand_id: value}}, { value: 'id', label: 'name' }).then((data) => (category.value = data))

const selectCategory = (value) => {
  getProducts(value)
}


const products = ref({ options: [], list: [] })

const getProducts = (value) => useSelect({ url: PRODUCT_LIST, data: {category_id: value}}, { value: 'id', label: 'name' }).then((data) => (products.value = data))

useOnOffcanvasHide('form-offcanvas-stock', () => setFormData(defaultData()))

/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    location_id: 1,
    product_id: '',
    has_variation: 0,
    product_variation_id: '',
    stock: 0,
    category_id: [],
    brand_id: 0,
    variations: []
  }
}



//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      location_id: data.location_id,
      product_id: data.product_id,
      has_variation: data.has_variation,
      product_variation_id: data.product_variation_id,
      stock: data.stock,
      category_id: data.category_id,
      brand_id: data.brand_id,
      variations: data.variations
    }
  })
}

const singleSelectOption = ref({
closeOnSelect: true,
searchable: true
})

const multiselectOption = ref({
  mode: 'tags',
  searchable: true
})

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    renderedDataTable.ajax.reload(null, false)
    bootstrap.Offcanvas.getInstance('#form-offcanvas-stock').hide()
    setFormData(defaultData())
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Validations
const validationSchema = yup.object({
})


const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: location_id } = useField('location_id')
const { value: product_id } = useField('product_id')
const { value: has_variation } = useField('has_variation')
const { value: product_variation_id } = useField('product_variation_id')
const { value: stock } = useField('stock')
const { value: category_id } = useField('category_id')
const { value: brand_id } = useField('brand_id')
const { fields: variations, replace: variationsReplace} = useFieldArray('variations')

const errorMessages = ref({})

// Form Submit

const formSubmit = handleSubmit((values) => {
  storeRequest({ url: STOCK_STORE_URL, body: values})
    .then((res) => reset_datatable_close_offcanvas(res));
});


const getProductVariationsData = () => {
  const data = {
    location_id: location_id.value,
    product_id: product_id.value
  }
  if(product_id.value) {
    getRequest({url: GET_PRODUCT_VARIATIONS_LIST, id: data}).then((res) => {
      variationsReplace([])
      product_variation_id.value = null
      stock.value = 0
      has_variation.value = res.has_variation
      if(res.has_variation) {
        variationsReplace(res.data)
      } else {
        product_variation_id.value = res.data.product_variation_id
        stock.value = res.data.stock
      }
    })
  }
}


</script>
