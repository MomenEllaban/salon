<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="$t('earning.pay_out_to')" :createTitle="$t('earning.pay_out_to')"></FormHeader>
      <div class="offcanvas-body">
        <div class="border-bottom">
          <div class="d-flex align-items-center gap-3 mb-2">
            <img :src="profile_image" alt="avatar" class="img-fluid avatar avatar-60 rounded-pill" />
            <div class="flex-grow-1">
              <div class="gap-2">
                <strong>{{ full_name }}</strong>
                <p class="m-0">
                  <small>{{ email }}</small>
                </p>
                <p class="m-0">
                  <small>{{ mobile }}</small>
                </p>
              </div>
            </div>
          </div>
        </div>


        <div class="row" >
          <div class="col-12 py-2">
            <div class="form-group">
               <label class="form-label">{{ $t('earning.lbl_select_method') }} <span class="text-danger">*</span></label>

               <Multiselect id="payment_method" v-model="payment_method" :placeholder="$t('earning.select_method')" v-bind="singleSelectOption" :options="payment_method_data.options" @select="branchSelect" class="form-group"></Multiselect>
               <span class="text-danger">{{ errors.payment_method }}</span>
            </div>
          </div>


          <div class="col-12">
            <div class="form-group">
              <label class="form-label" for="name"> {{ $t('earning.lbl_description') }}  <span class="text-danger">*</span> </label>
              <textarea type="textarea" class="form-control" :placeholder="$t('earning.enter_decription')" v-model="description" id="description"></textarea>
              <span v-if="errorMessages['description']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.description }}</span>
            </div>
          </div>

          <div class="col-12 py-1">
            <div class="d-flex justify-content-between align-items-center">
              <span>{{ $t('earning.commission_earn') }}</span>
              <strong>{{ commission_earn }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span>{{ $t('earning.tip_earn') }}</span>
              <strong>{{ tip_earn }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center border-top py-3 mt-3">
              <span class="flex-grow-1">{{ $t('earning.total_pay') }}</span>
              <h6><strong>{{ amount }}</strong></h6>
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
import { EDIT_URL, STORE_URL, UPDATE_URL,LISTING_URL } from '../constant/earning'
import { useField, useForm } from 'vee-validate'

import { useModuleId, useRequest,useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { buildMultiSelectObject } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' }
})

const singleSelectOption = ref({
  searchable: true,
  createOption: true,
  clearable: false

})
  const formatCurrencyVue = window.currencyFormat

  const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

  const payment_method_data = ref([])

  const type = 'earning_payment_method'

listingRequest({ url: LISTING_URL, data: {type: type} }).then((res) => {
  payment_method_data.value.options = buildMultiSelectObject(res.results, {
    value: 'id',
    label: 'text'
  }).map(option => ({
    ...option,
    label: option.label.charAt(0).toUpperCase() + option.label.slice(1)
  }));
})

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

// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    description: '',
    amount: '',
    payment_method: ''
  }
}

const setFormData = (data) => {
  resetForm({
    values: {
      id: data.id,
      full_name: data.full_name,
      email: data.email,
      mobile: data.mobile,
      profile_image: data.profile_image,
      description: '',
      commission_earn: data.commission_earn,
      tip_earn: data.tip_earn,
      amount: data.amount,
      payment_method: data.payment_method
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
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

// Validations
const validationSchema = yup.object({
  payment_method: yup.string().required("Payment method is required field"),
  amount: yup.string().required(),
  description: yup.string().required("Description is required field")
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const fieldMappings = {
  id: useField('id'),
  full_name: useField('full_name'),
  email: useField('email'),
  mobile: useField('mobile'),
  profile_image: useField('profile_image'),
  description: useField('description'),
  commission_earn: useField('commission_earn'),
  tip_earn: useField('tip_earn'),
  amount: useField('amount'),
  payment_method: useField('payment_method')
};

// Access the values using destructuring
const { value: id } = fieldMappings.id;
const { value: full_name } = fieldMappings.full_name;
const { value: email } = fieldMappings.email;
const { value: mobile } = fieldMappings.mobile;
const { value: profile_image } = fieldMappings.profile_image;
const { value: description } = fieldMappings.description;
const { value: commission_earn } = fieldMappings.commission_earn;
const { value: tip_earn } = fieldMappings.tip_earn;
const { value: amount } = fieldMappings.amount;
const { value: payment_method } = fieldMappings.payment_method;

const errorMessages = ref({})

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
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
.form__input {
  font-family: 'Roboto', sans-serif;
  color: #333;
  font-size: 1.2rem;
	margin: 0 auto;
  padding: 1.5rem 2rem;
  border-radius: 0.2rem;
  background-color: rgb(255, 255, 255);
  border: none;
  width: 90%;
  display: block;
  border-bottom: 0.3rem solid transparent;
  transition: all 0.3s;
}
</style>
