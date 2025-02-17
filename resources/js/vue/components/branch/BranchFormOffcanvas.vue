<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12 row">
            <div class="col-12 row">
              <div class="col-md-6">
                <InputField class="col-md-12" :is-required="true" :label="$t('branch.lbl_branch_name')" :placeholder="$t('branch.branch_name')" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>
                <div class="form-group col-md-12">
                  <label class="form-label">{{ $t('branch.lbl_branch_for') }}</label>
                  <div class="btn-group w-100" role="group" aria-label="Basic example">
                    <template v-for="(item, index) in BRANCH_FOR_OPTIONS" :key="index">
                      <input type="radio" class="btn-check" name="branch_for" :id="`${item.id}-for`" :value="item.id" autocomplete="off" v-model="branch_for" :checked="branch_for == item.id" />
                      <label class="btn btn-outline-primary" :for="`${item.id}-for`">{{ item.text }}</label>
                    </template>
                  </div>
                </div>
              </div>
              <div class="form-group col-md-6">
                <div class="text-center">
                  <img :src="ImageViewer || defaultImage" alt="feature-image" class="img-fluid mb-2 avatar-140 avatar-rounded" />
                  <div v-if="validationMessage" class="text-danger mb-2">{{ validationMessage }}</div>
                  <div class="d-flex align-items-center justify-content-center gap-2">
                    <input type="file" ref="profileInputRef" class="form-control d-none" id="feature_image" name="feature_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
                    <label class="btn btn-info" for="feature_image">{{ $t('messages.upload') }}</label>
                    <input type="button" class="btn btn-danger" name="remove" :value="$t('messages.remove')" @click="removeLogo()" v-if="ImageViewer" />
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-md-12">
              <div class="d-flex justify-content-between">
                <label for="manager_id">{{ $t('branch.lbl_select_manager') }} <span class="text-danger">*</span></label>
                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-sm text-primary"><i class="fa-solid fa-plus"></i> {{ $t('messages.create') }} {{ $t('messages.new') }}</button>
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
              <label class="form-label" for="services">{{ $t('branch.lbl_select_service') }}</label>
              <Multiselect v-model="service_id" :value="service_id" :options="service.options" :placeholder="$t('branch.select_service')" v-bind="multiselectOption" id="services"></Multiselect>
            </div>
            <div class="form-group col-md-6">
              <label class="form-label"> {{ $t('branch.lbl_contact_number') }} <span class="text-danger">*</span> </label>
              <vue-tel-input type="number" :value="contact_number" @input="handleInput" v-bind="{mode: 'international',maxLen: 15}"></vue-tel-input>
              <span class="text-danger">{{ errors['contact_number'] }}</span>
            </div>
            <InputField class="col-md-6" :is-required="false" :label="$t('branch.lbl_contact_email')" :placeholder="$t('branch.enter_email')" v-model="contact_email" :error-message="errors.contact_email" :error-messages="errorMessages['contact_email']"></InputField>
            <InputField class="col-md-6" :is-required="true" :label="$t('branch.lbl_shop_number')" :placeholder="$t('branch.enter_landmark')" v-model="address_line_1" :error-message="errors['address.address_line_1']" :error-messages="errorMessages['address_line_1']"></InputField>
            <InputField class="col-md-6" :label="$t('branch.lbl_landmark')" :placeholder="$t('branch.enter_nearby')" v-model="address_line_2" :error-message="errors['address.address_line_2']" :error-messages="errorMessages['address_line_2']"></InputField>
            <div class="col-md-3 form-group">
            <label class="form-label">{{ $t('branch.lbl_country') }} </label>
            <Multiselect id="country-list" v-model="country" :value="country" :placeholder="$t('branch.select_country')" v-bind="singleSelectOption" :options="countries.options" @select="getState" class="form-group"></Multiselect>
            <span v-if="errorMessages['country']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['country']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors['address.country'] }}</span>
          </div>
          <div class="col-md-3 form-group">
            <label class="form-label">{{ $t('branch.lbl_state') }} </label>
            <Multiselect id="state-list" v-model="state" :value="state" v-bind="singleSelectOption" :placeholder="$t('branch.select_state')" :options="states.options" @select="getCity" class="form-group"></Multiselect>
            <span v-if="errorMessages['state']">
              <ul class="text-danger">
                <li v-for="err in errorMessages['state']" :key="err">{{ err }}</li>
              </ul>
            </span>
            <span class="text-danger">{{ errors['address.state'] }}</span>
          </div>
          <div class="col-md-3 form-group">
            <label class="form-label">{{ $t('branch.lbl_city') }}</label>
            <Multiselect id="city-list" v-model="city" :value="city" v-bind="singleSelectOption" :placeholder="$t('branch.select_city')" :options="cities.options" class="form-group"></Multiselect>
            <span v-if="errorMessages['city']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['city']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors['address.city'] }}</span>
          </div>
            <InputField class="col-md-3" type="text" :is-required="true" :label="$t('branch.lbl_postal_code')" :placeholder="$t('branch.select_code')" v-model="postal_code" :error-message="errors['address.postal_code']" :error-messages="errorMessages['postal_code']"></InputField>
            <InputField class="col-md-3" :is-required="false" :label="$t('branch.lbl_lat')" :placeholder="$t('branch.enter_latitutude')" v-model="latitude" :error-message="errors['address.latitude']" :error-messages="errorMessages['latitude']"></InputField>
            <InputField class="col-md-3" :is-required="false" :label="$t('branch.lbl_long')" :placeholder="$t('branch.enter_logtitude')" v-model="longitude" :error-message="errors['address.longitude']" :error-messages="errorMessages['longitude']"></InputField>


              <div class="form-group col-md-6">
              <label class="form-label" for="payment-method">{{ $t('branch.lbl_payment_method') }} <span class="text-danger">*</span></label>
              <div class="d-flex w-100 gap-3" role="group" aria-label="Basic checkbox toggle button group">
                <template v-for="(item, index) in PAYMENT_METHODS_OPTIONS" :key="index">
                  <div class="d-flex gap-1 form-check">
                    <input type="checkbox" class="form-check-input" :id="`${item.id}-payment-method`" autocomplete="off" :value="item.id" v-model="payment_method" :checked="payment_method.includes(item.id)" />
                    <label class="form-label mb-0" :for="`${item.id}-payment-method`">{{ item.text }}</label>
                  </div>
                </template>
              </div>
              <span class="text-danger">{{ errors.payment_method }}</span>
            </div>

            <div class="form-group col-md-12">
              <label class="form-label" for="description">{{$t('branch.lbl_description')}}</label>
              <textarea class="form-control" v-model="description" :placeholder="$t('branch.enter_decription')" id="description"></textarea>
              <span v-if="errorMessages['description']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.description }}</span>
            </div>

            <div v-for="field in customefield" :key="field.id">
              <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value"  :field_id="field.id"  ></FormElement>            </div>

            <div class="form-group col-md-2 ">
              <div class="d-flex gap-3">
                <label class="form-label" for="category-status">{{$t('branch.lbl_status')}}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="status" :checked="status" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <FormFooter :IS_SUBMITED="IS_SUBMITED"></FormFooter>
    </div>
  </form>
  <EmployeeCreate @submit="updateManagerDetail"></EmployeeCreate>
</template>
<script setup>
import { ref, reactive, onMounted } from 'vue'
import { EDIT_URL, STORE_URL, UPDATE_URL, SERVICE_LIST, EMPLOYEE_LIST, COUNTRY_URL, STATE_URL, CITY_URL} from '../../constants/branch'
import { useField, useForm } from 'vee-validate'
import { readFile } from '@/helpers/utilities'
import { useModuleId, useRequest,useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import { VueTelInput } from 'vue3-tel-input'
import * as yup from 'yup'
import { useSelect } from '@/helpers/hooks/useSelect'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
// Modals
import EmployeeCreate from '@/vue/components/Modal/EmployeeCreate.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  selectData: { type: String },
  customefield: { type: Array, default: () => [] },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' }
})

const parseSelectData = JSON.parse(props.selectData)

const { getRequest, storeRequest, updateRequest } = useRequest()

const BRANCH_FOR_OPTIONS = reactive(parseSelectData['BRANCH_FOR'] || [])

const PAYMENT_METHODS_OPTIONS = reactive(parseSelectData['PAYMENT_METHODS'])

const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})
const multiselectOption = ref({
  mode: 'tags',
  searchable: true
})

const service = ref({ options: [], list: [] })
const manager = ref({ options: [], list: [] })

const getServiceList = (cb = null) => {
  useSelect({ url: SERVICE_LIST }, { value: 'id', label: 'name' }).then((data) => (service.value = data))
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

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))

onMounted(() => {
  getServiceList()
  getManagerList()
  getCountry()
  setFormData(defaultData())
})

const numberRegex = /^\d+$/;

/*
 * Form Data & Validation & Handeling
 */

 const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
 const validationSchema = yup.object({
  name: yup.string()
    .required('Branch Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => {
      if (!value) return true; // Allow empty value
      const specialCharsRegex = /[!@#$%^&*,.?":{}|<>\-_;'\/+=\[\]\\]/;
      const numberRegex = /\d/;
      return !specialCharsRegex.test(value) && !numberRegex.test(value);
    }),
  manager_id: yup.string()
    .required('Assign Manager is a required field')
    .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
  contact_number: yup.string()
    .required('Contact Number is a required field')
    .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),

  contact_email: yup.string()
    .nullable()
    .notRequired()
    .test('valid-email', 'Must be a valid email', (value) => {
      return !value || EMAIL_REGX.test(value); // Validate only if value exists
    }),

  address: yup.object({
    address_line_1: yup.string().required('Address is a required field'),
    postal_code: yup.string()
      .nullable()
      .notRequired()
      .test('valid-postal', 'Postal code must be a valid number', (value) => {
        return !value || /^\d+$/.test(value); // Validate only if value exists
      }),
    city: yup.string()
      .nullable()
      .notRequired()
      .test('state-selected', 'Select state first', function (value) {
        const { state } = this.parent;
        return !value || !!state; // Only validate if value exists
      }),
    latitude: yup.string()
      .nullable()
      .notRequired()
      .test('valid-lat', 'Latitude must be a valid number', (value) => {
        return !value || /^-?\d+(\.\d+)?$/.test(value); // Validate only if value exists
      }),
    longitude: yup.string()
      .nullable()
      .notRequired()
      .test('valid-long', 'Longitude must be a valid number', (value) => {
        return !value || /^-?\d+(\.\d+)?$/.test(value); // Validate only if value exists
      }),
    state: yup.string()
      .nullable()
      .notRequired()
      .test('country-selected', 'Select country first', function (value) {
        const { country } = this.parent;
        return !value || (country && country !== '');
      }),
    country: yup.string().nullable().notRequired(),
    landmark: yup.string().nullable().notRequired(),
  })
});



// const validationSchema = yup.object({
//   name: yup.string()
//     .required('Branch Name is a required field')
//     .test('is-string', 'Only strings are allowed', (value) => {
//       // Regular expressions to disallow special characters and numbers
//       const specialCharsRegex = /[!@#$%^&*,.?":{}|<>\-_;'\/+=\[\]\\]/;
//       return !specialCharsRegex.test(value) && !numberRegex.test(value);
//     }),
//   manager_id:  yup.string().required('Assign Manager is a required field').matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
//   contact_number: yup.string().required('Contact Number is a required field').matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
//   contact_email: yup.string().matches(EMAIL_REGX, 'Must be a valid email'),
//   address: yup.object({
//     address_line_1: yup.string().required('Address is a required field'),
//     postal_code: yup.string(),
//     city: yup.string()
//       .test('state-selected', 'Select state first', function (value) {
//         const { state } = this.parent;
//         return !value || !!state; // Pass if city is empty or state is selected
//       }),
//     latitude: yup.string()
//     .matches(/^(-?\d+(\.\d+)?)$/, 'Latitude must be a valid number'),
//     longitude: yup.string()
//     .matches(/^(-?\d+(\.\d+)?)$/, 'Longitude must be a valid number'),
//     state: yup.string()
//       .test('country-selected', 'Select country first', function (value) {
//         const { country } = this.parent;
//         // Check if country is not selected, show 'Select country first'
//         return !value || (country && country !== '');  // If state is selected, country must be selected too
//       }),
//     country: yup.string(),
//     // manager_id: yup.string().required('Manager is a required field'),
//   })
// })

// how to add contact_email validation first letter accept only not digit if first letter then add valid email validation in vue

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema,
  initialValues: {
    payment_method: ['cash']
  }
})

const { value: name } = useField('name')
const { value: postal_code } = useField('address.postal_code')
const { value: address_line_1 } = useField('address.address_line_1')
const { value: address_line_2 } = useField('address.address_line_2')
const { value: country } = useField('address.country')
const { value: state } = useField('address.state')
const { value: city } = useField('address.city')
const { value: latitude } = useField('address.latitude')
const { value: longitude } = useField('address.longitude')
const { value: status } = useField('status')
const { value: branch_for } = useField('branch_for')
const { value: payment_method } = useField('payment_method')
const { value: manager_id } = useField('manager_id')
const { value: service_id } = useField('service_id')
const { value: contact_email } = useField('contact_email')
const { value: contact_number } = useField('contact_number')
const { value: feature_image } = useField('feature_image')
const { value: description } = useField('description')
const { value: custom_fields_data } = useField('custom_fields_data')

const updateManagerDetail = (e) => {
  switch (e.type) {
    case 'create_manager':
      getManagerList(() => manager_id.value = e.value)
        break;

    default:
      break;
  }
}

branch_for.value = 'both'

// Default FORM DATA, Error Message
const errorMessages = ref({})
const validationMessage = ref('');

// File Upload Function
const ImageViewer = ref(null)
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

const defaultData = () => {
  ImageViewer.value = props.defaultImage
  errorMessages.value = {}
  return {
    name: '',
    contact_email:'',
    contact_number:'',
    feature_image: null,
    address: {
      postal_code: '',
      city: '',
      latitude: '',
      longitude: '',
      state: '',
      country: '',
      address_line_1: '',
      address_line_2: ''
    },
    description:'',
    manager_id: null,
    status: true,
    branch_for: 'both',
    service_id: [],
    payment_method: ['cash'],
    custom_fields_data: {}
  }
}

//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.feature_image || props.defaultImage
  resetForm({
    values: {
      name: data.name,
      contact_email:data.contact_email,
      contact_number:data.contact_number,
      feature_image: data.feature_image,
      address: {
        postal_code: data.address.postal_code,
        city: data.address.city,
        latitude: data.address.latitude,
        longitude: data.address.longitude,
        state: data.address.state,
        country: data.address.country,
        address_line_1: data.address.address_line_1,
        address_line_2: data.address.address_line_2
      },
      status: data.status,
      description: data.description,
      branch_for: data.branch_for,
      manager_id: data.manager_id,
      service_id: data.service_id,
      payment_method: data.payment_method,
      custom_fields_data: data.custom_field_data
    }
  })
}

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    contact_number.value = phoneObject.formatted
  }
};

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
      if(res.status) {
        getManagerList(() => manager_id.value = res.data.manager_id)
        setFormData(res.data)
        getState(country.value)
        getCity(state.value)

      }
    }
    )
  } else {
    setFormData(defaultData())
  }
})

const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false

  // if(props.customefield > 0) {
  //   values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  //   console.log(values.custom_fields_data );
  // }

  if (props.customefield.length > 0) {
    const plainCustomFieldsData = JSON.parse(JSON.stringify(values.custom_fields_data));
    values.custom_fields_data = JSON.stringify(plainCustomFieldsData);
  }
  if (currentId.value > 0) {
    // Convert address object to JSON string
    values.address = JSON.stringify(values.address);
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    // Convert address object to JSON string
    values.address = JSON.stringify(values.address);
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
    removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}
</script>

<style scoped>
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
</style>

