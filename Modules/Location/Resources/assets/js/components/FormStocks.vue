<template>
  <form @submit="formSubmit">
    <div class="form-group">
      <label class="form-label">Location</label>
      <Multiselect id="location_id-list" v-model="location_id" :value="location_id" v-bind="singleSelectOption" :options="LOCATIONS_OPTIONS" class="form-group" @select="getProductVariationsData"></Multiselect>
      <span class="text-danger">{{ errors['location_id'] }}</span>
      <ul class="m-0">
        <li class="text-danger" v-for="msg in errorMessages['location_id']" :key="msg">{{ msg }}</li>
      </ul>
    </div>
    <div class="form-group">
      <label class="form-label">Products</label>
      <Multiselect id="product_id-list" v-model="product_id" :value="product_id" v-bind="singleSelectOption" :options="PRODUCT_OPTIONS" class="form-group" @select="getProductVariationsData"></Multiselect>
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
                <label for="" class="control-label">Variation</label>
              </th>
              <th data-breakpoints="xs sm">
                <label for="" class="control-label">Stock</label>
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
    <div class="form-group">
      <button type="submit" class="btn btn-primary" :disabled="!product_id">Submit</button>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { STOCK_STORE_URL, GET_PRODUCT_VARIATIONS_LIST } from '../constant'
import { useField, useForm, useFieldArray } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'

import {useRequest } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

// props
const props = defineProps({
  locations: {type: Array, default: () => []},
  products: {type: Array, default: () => []}
})

console.log(props)
const PRODUCT_OPTIONS = ref(props.products)

const LOCATIONS_OPTIONS = ref(props.locations)

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const { getRequest, storeRequest } = useRequest()

const getProductVariationsData = () => {
  const data = {
    location_id: location_id.value,
    product_id: product_id.value
  }
  if(location_id.value && product_id.value) {
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
/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    location_id: '',
    product_id: '',
    has_variation: 0,
    product_variation_id: '',
    stock: 0,
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
      variations: data.variations
    }
  })
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const reset_datatable_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    setFormData(defaultData())
    removeImage()
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
const { fields: variations, replace: variationsReplace} = useFieldArray('variations')

const errorMessages = ref({})

onMounted(() => {
  setFormData(defaultData())
})

const formSubmit = handleSubmit((values) => {
  console.log(values)
  storeRequest({ url: STOCK_STORE_URL, body: values})
    .then((res) => reset_datatable_close_offcanvas(res));
});

</script>
