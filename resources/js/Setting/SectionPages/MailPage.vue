<template>
  <form @submit="formSubmit">
    <CardTitle :title="$t('setting_sidebar.lbl_mail')" icon="fas fa-envelope"></CardTitle>
    <div class="row row-cols-2">
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_email')" placeholder="info@example.com" v-model="email" :error-message="errors.email"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_driver')" placeholder="smtp" v-model="mail_driver" :error-message="errors.mail_driver"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_host')" placeholder="smtp.gmail.com" v-model="mail_host" :error-message="errors.mail_host"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_port')" placeholder="587" v-model="mail_port" :error-message="errors.mail_port"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_encryption')" placeholder="tls" v-model="mail_encryption" :error-message="errors.mail_encryption"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_username')" placeholder="youremail@gmail.com" v-model="mail_username" :error-message="errors.mail_username"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_password')" type="password" placeholder="Password" v-model="mail_password" :error-message="errors.mail_password"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_mail')" placeholder="youremail@gmail.com" v-model="mail_from" :error-message="errors.mail_from"></InputField>
      <InputField class="col" :is-required="true" :label="$t('setting_mail_page.lbl_from_name')" placeholder="Frezka" v-model="from_name" :error-message="errors.from_name"></InputField>
    </div>
    <div class="d-grid d-md-flex gap-3 align-items-center">
      <SubmitButton :IS_SUBMITED="IS_SUBMITED" v-if="isEmailVerified"></SubmitButton>
      <button type="button" class="btn btn-primary" :disabled="IS_VERIFY_SUBMITED" @click="verifyEmail">
        <template v-if="IS_VERIFY_SUBMITED">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Verifing...
        </template>
        <template v-else>
          {{$t('messages.verify_email')}}
        </template>
      </button>
      <span v-if="isEmailVerified" class="badge bg-soft-success">{{ VARIFY_MESSAGE }}</span>
      <span v-else class="badge bg-soft-danger">{{ VARIFY_MESSAGE }}</span>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL, VERIFIED_EMAIL } from '@/vue/constants/setting'

import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'

const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
const IS_VERIFY_SUBMITED = ref(false)

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      email: data.email,
      mail_driver: data.mail_driver,
      mail_host: data.mail_host,
      mail_port: data.mail_port,
      mail_encryption: data.mail_encryption,
      mail_username: data.mail_username,
      mail_password: data.mail_password,
      mail_from: data.mail_from,
      from_name: data.from_name
    }
  })
}

const numberRegex = /^\d+$/
const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/
//validation
const validationSchema = yup.object({
  email: yup
    .string()
    .required('Email is required')
    .test('is-string', 'First strings are allowed', (value) => !numberRegex.test(value))
    .matches(EMAIL_REGX, 'Must be a valid email'),
  mail_driver: yup.string().required('mail driver is required'),
  mail_host: yup.string().required('mail host is required'),
  mail_port: yup.string().required('mail port is required'),
  mail_encryption: yup.string().required('mail encryption is required'),
  mail_username: yup
    .string()
    .required('mail username is required')
    .test('is-string', 'First strings are allowed', (value) => !numberRegex.test(value)),
  mail_password: yup.string().required('Password is required'),
  mail_from: yup
    .string()
    .required('mail from is required')
    .test('is-string', 'First strings are allowed', (value) => !numberRegex.test(value))
    .matches(EMAIL_REGX, 'Must be a valid email'),
  from_name: yup.string().required('from name is required')
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })

const { value: email } = useField('email')
const { value: mail_driver } = useField('mail_driver')
const { value: mail_host } = useField('mail_host')
const { value: mail_port } = useField('mail_port')
const { value: mail_encryption } = useField('mail_encryption')
const { value: mail_username } = useField('mail_username')
const { value: mail_password } = useField('mail_password')
const { value: mail_from } = useField('mail_from')
const { value: from_name } = useField('from_name')

//fetch data
const data = 'email,mail_driver,mail_host,mail_port,mail_encryption,mail_username,mail_from,from_name,mail_password'

onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

const isEmailVerified = ref(false)

const VARIFY_MESSAGE = ref(null)

const verifyEmail = () => {
  IS_VERIFY_SUBMITED.value = true
  const mailObject = {
    email: email.value,
    driver: mail_driver.value,
    host: mail_host.value,
    port: mail_port.value,
    encryption: mail_encryption.value,
    username: mail_username.value,
    password: mail_password.value,
    from: {
      address: mail_from.value,
      name: from_name.value
    }
  }

  watch(
    [email, mail_driver, mail_host, mail_port, mail_encryption, mail_username, mail_password, mail_from, from_name],
    () => {
      isEmailVerified.value = false // Reset isEmailVerified when any input field changes
      VARIFY_MESSAGE.value = "Please reverify the changed SMTP configuration."
    },
    { deep: true }
  )

  storeRequest({ url: VERIFIED_EMAIL, body: mailObject })
    .then((res) => {
      if (res.status) {
        isEmailVerified.value = true

        VARIFY_MESSAGE.value = 'Verified Successfully!'
      } else {
        isEmailVerified.value = false
        VARIFY_MESSAGE.value = 'Please check your smtp configuration!'
      }
      IS_VERIFY_SUBMITED.value = false
    })
    .catch((err) => {
      IS_VERIFY_SUBMITED.value = false
    })
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

//Form Submit
const formSubmit = handleSubmit((values) => {
  if (isEmailVerified.value) {
    // Proceed with form submission
    IS_SUBMITED.value = true
  }
  storeRequest({ url: STORE_URL, body: values }).then((res) => display_submit_message(res))
})
</script>
