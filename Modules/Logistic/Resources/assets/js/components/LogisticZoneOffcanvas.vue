<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <InputField class="col-md-12" type="text" :is-required="true" :label="$t('logistic_zone.lbl_name')" :placeholder="$t('logistic_zone.name')" v-model="name" :error-message="errors['name']"></InputField>
        <InputField class="col-md-12" type="textarea" :is-required="true" :label="$t('promotion.address')" placeholder="Enter address..." v-model="description" :error-message="errors['description']" :error-messages="errorMessages['description']"></InputField>

        <div class="form-group col-md-12">
                  <label class="form-label">{{ $t('employee.lbl_phone_number') }}<span class="text-danger">*</span>
                  </label>
                  <vue-tel-input :value="mobile" @input="handleInput"
                    v-bind="{ mode: 'international', maxLen: 15 }" ></vue-tel-input>
                  <span class="text-danger">{{ errors['mobile'] }}</span>
        </div>
        <div class="col-md-12 form-group">
          <label class="form-label">{{ $t('logistic_zone.logistic') }} <span class="text-danger">*</span></label>
          <Multiselect id="logistic-list" v-model="logistic_id" :placeholder="$t('logistic_zone.logistic')" :value="logistic_id" v-bind="singleSelectOption" :options="logistics.options" class="form-group"></Multiselect>
          <span v-if="errorMessages['logistic_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['logistic_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.logistic_id }}</span>
        </div>
        <div class="col-md-12 form-group">
          <label class="form-label">{{ $t('logistic_zone.country') }} <span class="text-danger">*</span></label>
          <Multiselect id="country-list" v-model="country_id" :placeholder="$t('logistic_zone.select_country')" :value="country_id" v-bind="singleSelectOption" :options="countries.options" @select="getState" class="form-group"></Multiselect>
          <span v-if="errorMessages['country_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['country_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.country_id }}</span>
        </div>
        <div class="col-md-12 form-group">
          <label class="form-label">{{ $t('logistic_zone.state') }} <span class="text-danger">*</span></label>
          <Multiselect id="state-list" v-model="state_id" :placeholder="$t('logistic_zone.select_state')" :value="state_id" v-bind="singleSelectOption" :options="states.options" @select="getCity" class="form-group"></Multiselect>
          <span v-if="errorMessages['state_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['state_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.state_id }}</span>
        </div>
        <div class="col-md-12 form-group">
          <label class="form-label">{{ $t('logistic_zone.cities') }}<span class="text-danger">*</span></label>
          <Multiselect id="city-list" v-model="city_id" :placeholder="$t('logistic_zone.select_city')" :value="city_id" v-bind="multiselectOption" :options="cities.options" class="form-group"></Multiselect>
          <span v-if="errorMessages['city_id']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['city_id']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.city_id }}</span>
        </div>
        <InputField class="col-md-12" type="number" :label="`${$t('logistic_zone.standard_delivery_charge')} ${currencySymbol}`" placeholder="" v-model="standard_delivery_charge" :error-message="errors['standard_delivery_charge']" />
        <InputField class="col-md-12" type="text" :label="$t('logistic_zone.standard_delivery_time')" :placeholder="$t('logistic_zone.delivery_time')" v-model="standard_delivery_time" :error-message="errors['standard_delivery_time']"></InputField>
      </div>
      <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
      </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, LOGISTIC_URL, COUNTRY_URL, STATE_URL, CITY_URL } from '../logistic-zone'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'
import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'

// props
defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] }
})

const { getRequest, storeRequest, updateRequest } = useRequest()

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const multiselectOption = ref({
  mode: 'tags',
  searchable: true
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
    mobile: '',
    logitic_id: '',
    standard_delivery_charge:'',
    country_id: '',
    state_id: '',
    city_id: [],
    standard_delivery_time: '1 Day'
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      mobile: data.mobile,
      description: data.description,
      logistic_id: data.logistic_id,
      standard_delivery_charge: data.standard_delivery_charge,
      standard_delivery_time: data.standard_delivery_time,
      country_id: data.country_id,
      state_id: data.state_id,
      city_id: data.city_id
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
  name: yup.string().required('Name is a required field'),
  description: yup.string().required('Address is a required field'),
  logistic_id: yup.string().required('Logitic is a required field'),
  country_id: yup.string().required('Country is a required field'),
  state_id: yup.string().required('State is a required field'),
  city_id: yup.array().test('city_id', 'City is a required field', function (value) {
    if (value.length == 0) {
      return false
    }
    return true
  }),
  standard_delivery_charge: yup.number().typeError('Standard delivery charge must be a number').min(0, 'Standard delivery charge must be greater than or equal to 0'),
  mobile: yup.string().required('Phone No is a required field').matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits')

})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: name } = useField('name')
const { value: logistic_id } = useField('logistic_id')
const { value: country_id } = useField('country_id')
const { value: state_id } = useField('state_id')
const { value: city_id } = useField('city_id')
const { value: standard_delivery_charge } = useField('standard_delivery_charge')
const { value: standard_delivery_time } = useField('standard_delivery_time')
const { value: mobile } = useField('mobile')
const { value: description } = useField('description')

const errorMessages = ref({})

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
}

onMounted(() => {
  setFormData(defaultData())
  getCountry()
})
const currencySymbol = window.defaultCurrencySymbol

const logistics = ref({ options: [], list: [] })

useSelect({ url: LOGISTIC_URL }, { value: 'id', label: 'name' }).then((data) => (logistics.value = data))

const countries = ref({ options: [], list: [] })

const getCountry = () => {
  useSelect({ url: COUNTRY_URL }, { value: 'id', label: 'name' }).then((data) => (countries.value = data))
}

const states = ref({ options: [], list: [] })

const getState = (value) => {
  console.log(value)
  useSelect({ url: STATE_URL, data: value }, { value: 'id', label: 'name' }).then((data) => (states.value = data))
}

const cities = ref({ options: [], list: [] })

const getCity = (value) => {
  useSelect({ url: CITY_URL, data: value }, { value: 'id', label: 'name' }).then((data) => (cities.value = data))
}

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status) {
        setFormData(res.data)
        getState(country_id.value)
        getCity(state_id.value)
      }
    })
  } else {
    setFormData(defaultData())
  }
})
// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if (IS_SUBMITED.value) return false
  IS_SUBMITED.value = true
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
</script>
