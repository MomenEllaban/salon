<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center">
      <CardTitle title="Branch Setting" icon="fa-solid fa-code-branch"></CardTitle>
    </div>
    <div class="row">
      <div class="col-12 row">
        <div class="d-flex align-items-center">
          <div class="col-6">
            <InputField :is-required="true" :label="$t('branch.lbl_branch_name')" placeholder="" v-model="name" :error-message="errors.name"></InputField>
            <div class="form-group">
              <label class="form-label">{{ $t('branch.lbl_branch_for') }}</label>
              <div class="btn-group w-100" role="group" aria-label="Basic example">
                <template v-for="(item, index) in BRANCH_FOR_OPTIONS" :key="index">
                  <input type="radio" class="btn-check" name="branch_for" :id="`${item.id}-for`" :value="item.id" autocomplete="off" v-model="branch_for" :checked="branch_for == item.id" />
                  <label class="btn btn-outline-primary" :for="`${item.id}-for`">{{ item.text }}</label>
                </template>
              </div>
            </div>
          </div>
          <div class="col-md-6 text-center">
            <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2" alt="profile-image" />
            <div class="d-flex align-items-center justify-content-center gap-2">
              <input type="file" ref="logoInputRef" class="form-control d-none" id="feature_image" name="feature_image" accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
              <label class="btn btn-info" for="feature_image">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger" name="remove" value="Remove" @click="removeLogo()" v-if="ImageViewer" />
            </div>
            <span class="text-danger">{{ errors.feature_image }}</span>
          </div>
        </div>

        <div class="form-group col-md-12">
          <label class="form-label" for="services">{{ $t('branch.lbl_select_service') }}</label>
          <Multiselect v-model="service_id" :value="service_id" :options="service.options" v-bind="multiselectOption" id="services"></Multiselect>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label"> {{ $t('branch.lbl_contact_number') }} <span class="text-danger">*</span> </label>
          <vue-tel-input type="number" :value="contact_number" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }"></vue-tel-input>
          <span class="text-danger">{{ errors['contact_number'] }}</span>
        </div>
        <InputField class="col-md-6" :is-required="false" :label="$t('branch.lbl_contact_email')" placeholder="" v-model="contact_email" :error-message="errors.contact_email"></InputField>
        <InputField class="col-md-6" :is-required="true" :label="$t('branch.lbl_shop_number')" placeholder="" v-model="address_line_1" :error-message="errors['address.address_line_1']"></InputField>
        <InputField class="col-md-6" :label="$t('branch.lbl_landmark')" placeholder="" v-model="address_line_2" :error-message="errors['address.address_line_2']"></InputField>
        <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_country')" placeholder="" v-model="country" :error-message="errors['address.country']"></InputField>
        <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_state')" placeholder="" v-model="state" :error-message="errors['address.state']"></InputField>
        <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_city')" placeholder="" v-model="city" :error-message="errors['address.city']"></InputField>
        <InputField class="col-md-2" type="number" :is-required="true" :label="$t('branch.lbl_postal_code')" placeholder="" v-model="postal_code" :error-message="errors['address.postal_code']"></InputField>
        <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_lat')" placeholder="" v-model="latitude" :error-message="errors['address.latitude']"></InputField>
        <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_long')" placeholder="" v-model="longitude" :error-message="errors['address.longitude']"></InputField>

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
          <label class="form-label" for="description">{{ $t('branch.lbl_description') }}</label>
          <textarea class="form-control" v-model="description" id="description"></textarea>
          <span v-if="errorMessages['description']">
            <ul class="text-danger">
              <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
            </ul>
          </span>
          <span class="text-danger">{{ errors.description }}</span>
        </div>

        <!-- <div class="form-group col-md-6">
              <label class="form-label" for="feature_image">{{ $t('branch.lbl_feature_image') }}</label>
              <input type="file" class="form-control mb-3" id="feature_image" @change="fileUpload" accept=".jpeg, .jpg, .png, .gif" />
              <img :src="ImageViewer" alt="feature-img" class="img-fluid w-50 rounded" v-if="ImageViewer">
              <span v-if="errorMessages['feature_image']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['feature_image']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.feature_image }}</span>
            </div> -->
      </div>
      <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
    </div>
  </form>
</template>

<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import SubmitButton from './Forms/SubmitButton.vue'
import { ref, reactive, onMounted } from 'vue'
import { UPDATE_BRANCH_SETTING, SERVICE_LIST, GET_URL } from '@/vue/constants/branch'
import { useField, useForm } from 'vee-validate'
import { readFile } from '@/helpers/utilities'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { VueTelInput } from 'vue3-tel-input'
import * as yup from 'yup'
import { useSelect } from '@/helpers/hooks/useSelect'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { createRequest } from '@/helpers/utilities'
import { useRouter } from 'vue-router'

const IS_SUBMITED = ref(false)
const ROLES = ref(JSON.parse(document.querySelector('meta[name="auth_user_roles"]')?.getAttribute('content')) || [])
const { getRequest, storeRequest, updateRequest } = useRequest()

const BRANCH_FOR_OPTIONS = reactive([
  { id: 'unisex', text: 'Unisex' },
  { id: 'female', text: 'Female' },
  { id: 'male', text: 'Male' }
  // Add more options as needed
])

const PAYMENT_METHODS_OPTIONS = reactive([
  { id: 'upi', text: 'UPI' },
  { id: 'cash', text: 'Cash' },
  { id: 'razorpay', text: 'Razorpay' },
  { id: 'stripe', text: 'Stripe' }
  // Add more options as needed
])

const multiselectOption = ref({
  mode: 'tags',
  searchable: true
})

const service = ref({ options: [], list: [] })

const getServiceList = (cb = null) => {
  useSelect({ url: SERVICE_LIST }, { value: 'id', label: 'name' }).then((data) => (service.value = data))
}
const router = useRouter()
onMounted(() => {
  if (!ROLES.value.includes('manager')) {
    return router.push({ name: 'profile.info' })
  }
  getServiceList()
  setFormData(defaultData())
  createRequest(GET_URL()).then((response) => {
    if (response.status) {
      setFormData(response.data)
    }
  })
})

const numberRegex = /^\d+$/

const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/

const validationSchema = yup.object({
  name: yup
    .string()
    .required('Branch Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => {
      // Regular expressions to disallow special characters and numbers
      const specialCharsRegex = /[!@#$%^&*,.?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  contact_number: yup
    .string()
    .required('Contact Number is a required field')
    .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
  contact_email: yup.string().matches(EMAIL_REGX, 'Must be a valid email'),
  address: yup.object({
    address_line_1: yup.string().required('Address is a required field'),
    postal_code: yup.string(),
    city: yup.string(),
    latitude: yup.string(),
    longitude: yup.string(),
    state: yup.string(),
    country: yup.string()
  })
})

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
const { value: branch_for } = useField('branch_for')
const { value: payment_method } = useField('payment_method')
const { value: service_id } = useField('service_id')
const { value: contact_email } = useField('contact_email')
const { value: contact_number } = useField('contact_number')
const { value: feature_image } = useField('feature_image')
const { value: description } = useField('description')

branch_for.value = 'both'

// Default FORM DATA, Error Message
const errorMessages = ref({})

// File Upload Function
const ImageViewer = ref(null)

const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    logoInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const changeLogo = (e) => fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: feature_image })
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: feature_image })

const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    contact_email: '',
    contact_number: '',
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
    description: '',
    branch_for: 'both',
    service_id: [],
    payment_method: ['cash']
  }
}

//  Reset Form
const setFormData = (data) => {
  // Check if data is not undefined or null
  if (data) {
    ImageViewer.value = data.feature_image
    resetForm({
      values: {
        name: data.name,
        contact_email: data.contact_email,
        contact_number: data.contact_number,
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

        description: data.description,
        branch_for: data.branch_for,
        service_id: data.service_id,
        payment_method: data.payment_method
      }
    })
  } else {
    // If data is undefined or null, set the form data to default values
    ImageViewer.value = null
    resetForm()
  }
}

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    contact_number.value = phoneObject.formatted
  }
}

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  values.address = JSON.stringify(values.address)
  storeRequest({ url: UPDATE_BRANCH_SETTING, body: values, type: 'file' }).then((res) => display_submit_message(res))
})
</script>
