<template>
    <form @submit="formSubmit">
      <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
        <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
        <div class="offcanvas-body">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('Name')" placeholder="" v-model="name" :error-message="errors['name']" :error-messages="errorMessages['name']"></InputField>

          <div v-for="field in customefield" :key="field.id">
            <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value" :field_id="field.id"></FormElement>
          </div>

          <InputField class="col-md-12" :is-required="true" :label="$t('branch.lbl_shop_number')" placeholder="" v-model="address_line_1" :error-message="errors['address.address_line_1']" :error-messages="errorMessages['address_line_1']"></InputField>
          <InputField class="col-md-12" :label="$t('branch.lbl_landmark')" placeholder="" v-model="address_line_2" :error-message="errors['address.address_line_2']" :error-messages="errorMessages['address_line_2']"></InputField>

          <div class="form-group col-md-12">
            <label class="form-label">{{ $t('branch.lbl_country') }} </label>
            <Multiselect id="country" v-model="country" :value="country" v-bind="singleSelectOption" :options="countryList.options" @select="selectCountry" class="form-group"></Multiselect>
            <span class="text-danger">{{ errors['country'] }}</span>
            <ul class="m-0">
              <li class="text-danger" v-for="msg in errorMessages['country']" :key="msg">{{ msg }}</li>
            </ul>
          </div>


          <div class="form-group col-md-12">
            <label class="form-label" for="State">{{ $t('branch.lbl_state') }}</label>
            <Multiselect v-model="state" :value="state" :options="stateList.options" v-bind="singleSelectOption" id="state"  @select="selectState"></Multiselect>
          </div>

          <div class="form-group col-md-12">
            <label class="form-label" for="City">{{ $t('branch.lbl_city') }}</label>
            <Multiselect v-model="city" :value="city" :options="cityList.options" v-bind="singleSelectOption" id="city" ></Multiselect>
          </div>

          <InputField class="col-md-12" type="number"  :label="$t('branch.lbl_postal_code')" placeholder="" v-model="pincode" :error-message="errors['pincode']" :error-messages="errorMessages['pincode']"></InputField>

          <div class="form-group">
            <div class="d-flex justify-content-between align-items-center mt-3">
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
  import { EDIT_URL, STORE_URL, UPDATE_URL, COUNTRY_LIST, STATE_LIST,CITY_LIST} from '../constant'
  import { useField, useForm } from 'vee-validate'
  import InputField from '@/vue/components/form-elements/InputField.vue'
  import { useSelect } from '@/helpers/hooks/useSelect'
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
    if (currentId.value > 0) {
      getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
        if (res.status) {

          getState(country.value)
         if(res.data.state.value != 0) {
             state.value = res.data.state
          }

          getCity(state.value)
         if(res.data.city.value != 0) {
              city.value = res.data.city
          }
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
      address_line_1: '',
      address_line_2: '',
      country: '',
      state: '',
      city: '',
      pincode: '',
    }
  }



  //  Reset Form
  const setFormData = (data) => {
    ImageViewer.value = data.feature_image
    resetForm({
      values: {
        name: data.name,
        address_line_1: data.address_line_1,
        address_line_2: data.address_line_2,
        country: data.country,
        state: data.state,
        city: data.city,
        pincode: data.pincode,
      }
    })
  }

  const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})

const countryList = ref({ options: [], list: [] })

const getCountryList = () => {
  useSelect({ url: COUNTRY_LIST }, { value: 'id', label: 'name' }).then((data) => (countryList.value = data))
}


const selectCountry = (value) => {
    getState(value)
}

const stateList = ref({ options: [], list: [] })

const getState = (value) => useSelect({ url: STATE_LIST, data: {country_id: value}}, { value: 'id', label: 'name' }).then((data) => (stateList.value = data))

const selectState = (value) => {
    getCity(value)
}

const cityList = ref({ options: [], list: [] })

const getCity = (value) => useSelect({ url: CITY_LIST, data: {state_id: value}}, { value: 'id', label: 'name' }).then((data) => (cityList.value = data))



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
  const { value: address_line_1 } = useField('address_line_1')
  const { value: address_line_2 } = useField('address_line_2')
  const { value: country } = useField('country')
  const { value: state } = useField('state')
  const { value: city } = useField('city')
  const { value: pincode } = useField('pincode')


  const errorMessages = ref({})

  onMounted(() => {

    setFormData(defaultData())
    getCountryList()
    getState()
    getCity()
  })

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true
    values.custom_fields_data = JSON.stringify(values.custom_fields_data);
    if (currentId.value > 0) {
      updateRequest({ url: UPDATE_URL, id: currentId.value, body: values })
        .then((res) => reset_datatable_close_offcanvas(res))
    } else {
      storeRequest({ url: STORE_URL, body: values })
        .then((res) => reset_datatable_close_offcanvas(res))
    }
  });

  </script>
