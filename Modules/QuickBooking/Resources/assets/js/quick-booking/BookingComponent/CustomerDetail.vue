<template>
  <div class="card-list-data">
    <div class="row">
      <InputField class="col-md-6" :is-required="true" :label="$t('quick_booking.lbl_first_name')" :placeholder="$t('customer.first_name')" v-model="first_name" :error-message="errors.first_name" :error-messages="errorMessages['first_name']"></InputField>
      <InputField class="col-md-6" :is-required="true" :label="$t('quick_booking.lbl_last_name')" :placeholder="$t('customer.last_name')" v-model="last_name" :error-message="errors['last_name']" :error-messages="errorMessages['last_name']"></InputField>
    </div>

    <InputField :is-required="true" :label="$t('quick_booking.lbl_Email')" :placeholder="$t('customer.email_address')" v-model="email" :error-message="errors['email']" :error-messages="errorMessages['email']"></InputField>
    <div class="form-group">
      <label class="form-label">{{ $t('quick_booking.lbl_phone_number') }}<span class="text-danger">*</span> </label>
      <vue-tel-input :value="mobile" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }"></vue-tel-input>
      <span class="text-danger">{{ errors['mobile'] }}</span>
    </div>

    <div class="form-group col-md-4">
      <label for="" class="w-100">{{ $t('quick_booking.lbl_gender') }}</label>
      <div class="d-flex mt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male" />
          <label class="form-check-label" for="male"> Male </label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female" />
          <label class="form-check-label" for="female"> Female </label>
        </div>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other" />
          <label class="form-check-label" for="other"> Other </label>
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <button type="button" class="btn btn-secondary iq-text-uppercase" v-if="wizardPrev" @click="prevTabChange(wizardPrev)">{{ $t('quick_booking.lbl_back') }}</button>
    <button :disabled="IS_SUBMITED" class="btn btn-primary iq-text-uppercase" name="submit" v-if="wizardNext" @click="formSubmit">
      <template v-if="IS_SUBMITED">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Loading...
      </template>
      <template v-else> {{ $t('quick_booking.lbl_submit') }}</template>
    </button>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { useField, useForm } from 'vee-validate'
import { VueTelInput } from 'vue3-tel-input'
import InputField from '@/vue/components/form-elements/InputField.vue'
import * as yup from 'yup'
import { useQuickBooking } from '../../store/quick-booking'
const props = defineProps({
  wizardNext: {
    default: '',
    type: [String, Number]
  },
  wizardPrev: {
    default: '',
    type: [String, Number]
  }
})
/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    gender: ''
  }
}

const numberRegex = /^\d+$/
let EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
const validationSchema = yup.object({
  first_name: yup
    .string()
    .required('First Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => {
      // Regular expressions to disallow special characters and numbers
      const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  last_name: yup
    .string()
    .required('Last Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => {
      // Regular expressions to disallow special characters and numbers
      const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/
      return !specialCharsRegex.test(value) && !numberRegex.test(value)
    }),
  email: yup
    .string()
    .required('Email is a required field').matches(EMAIL_REGX, 'Must be a valid email'),
  mobile: yup.string().required('Phone No is a required field').matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: gender } = useField('gender')
const { value: mobile } = useField('mobile')
const errorMessages = ref({})
const IS_SUBMITED = ref(false)

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
}

// Form Submit
const emit = defineEmits(['tab-change', 'onReset'])
const prevTabChange = (val) => (emit('tab-change', val))
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  emit('tab-change', props.wizardNext)
})
const store = useQuickBooking()
watch(() => store.bookingResponse, (value) => {
  IS_SUBMITED.value = false
  resetForm(defaultData())
}, {deep: true})
watch(
  () => mobile.value,
  (value) => {
    store.updateUserValues({ key: 'mobile', value: value })
  },
  { deep: true }
)

watch(
  () => first_name.value,
  (value) => {
    store.updateUserValues({ key: 'first_name', value: value })
  },
  { deep: true }
)

watch(
  () => last_name.value,
  (value) => {
    store.updateUserValues({ key: 'last_name', value: value })
  },
  { deep: true }
)

watch(
  () => email.value,
  (value) => {
    store.updateUserValues({ key: 'email', value: value })
  },
  { deep: true }
)

watch(
  () => gender.value,
  (value) => {
    store.updateUserValues({ key: 'gender', value: value })
  },
  { deep: true }
)
</script>
