<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_integration')" icon="fa-solid fa-sliders"></CardTitle>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-is_google_login">{{ $t('setting_integration_page.lbl_google_login') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_google_login" :checked="is_google_login == 1 ? true : false" name="is_google_login" id="category-is_google_login" type="checkbox" v-model="is_google_login" />
        </div>
      </div>
    </div>
    <div v-if="is_google_login == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_secret_key')" placeholder="" v-model="google_secretkey" :error-message="errors['google_secretkey']" :error-messages="errorMessages['google_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_public_key')" placeholder="" v-model="google_publickey" :error-message="errors['google_publickey']" :error-messages="errorMessages['google_publickey']"></InputField>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-is_custom_webhook_notification">{{ $t('setting_integration_page.lbl_webhook') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_custom_webhook_notification" :checked="is_custom_webhook_notification == 1 ? true : false" name="is_custom_webhook_notification" id="category-is_custom_webhook_notification" type="checkbox" v-model="is_custom_webhook_notification" />
        </div>
      </div>
    </div>
    <div v-if="is_custom_webhook_notification == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_custom_webhook_content_key')" placeholder="" v-model="custom_webhook_content_key" :error-message="errors['custom_webhook_content_key']" :error-messages="errorMessages['custom_webhook_content_key']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_custom_webhook_url')" placeholder="" v-model="custom_webhook_url" :error-message="errors['custom_webhook_url']" :error-messages="errorMessages['custom_webhook_url']"></InputField>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-is_map_key">{{ $t('setting_integration_page.lbl_google') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_map_key" :checked="is_map_key == 1 ? true : false" name="is_map_key" id="category-is_map_key" type="checkbox" v-model="is_map_key" />
        </div>
      </div>
    </div>
    <div v-if="is_map_key == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_google_key')" placeholder="" v-model="google_maps_key" :error-message="errors['google_maps_key']" :error-messages="errorMessages['google_maps_key']"></InputField>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-is_application_link">{{ $t('setting_integration_page.lbl_application') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="is_application_link" :checked="is_application_link == 1 ? true : false" name="is_application_link" id="category-is_application_link" type="checkbox" v-model="is_application_link" />
        </div>
      </div>
    </div>
    <div v-if="is_application_link == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_playstore')" placeholder="" v-model="customer_app_play_store" :error-message="errors['customer_app_play_store']" :error-messages="errorMessages['customer_app_play_store']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_appstore')" placeholder="" v-model="customer_app_app_store" :error-message="errors['customer_app_app_store']" :error-messages="errorMessages['customer_app_app_store']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-isForceUpdate">{{ $t('setting_integration_page.lbl_isforceupdate') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="isForceUpdate" :checked="isForceUpdate == 1 ? true : false" name="isForceUpdate" id="category-isForceUpdate" type="checkbox" v-model="isForceUpdate" />
        </div>
      </div>
    </div>
    <div v-if="isForceUpdate == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_integration_page.lbl_version_code')" placeholder="" v-model="version_code" :error-message="errors['version_code']" :error-messages="errorMessages['version_code']"></InputField>
        </div>
      </div>
    </div>


    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="category-firebase_notification">{{ $t('setting_integration_page.lbl_firebase') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="firebase_notification" :checked="firebase_notification == 1 ? true : false" name="firebase_notification" id="category-firebase_notification" type="checkbox" v-model="firebase_notification" />
        </div>
      </div>
    </div>

    <div v-if="firebase_notification == 1">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group mb-0">
            <label class="form-label" for="firebase_project_id">{{ $t('setting_integration_page.lbl_firebase_project_id') }}</label>
            <input type="text" class="form-control" v-model="firebase_project_id" placeholder="Firebase Project ID" id="firebase_project_id" name="firebase_project_id" :errorMessage="errors.firebase_project_id" :errorMessages="errorMessages.firebase_project_id" />
            <p class="text-danger" v-for="msg in errorMessages.firebase_project_id" :key="msg">{{ msg }}</p>
            <span class="text-danger">{{ errors.firebase_project_id }}</span>
          </div>
        </div>
        <div class="form-group col-sm-6 mb-0">
          <label for="json_file" class="form-control-label">
            {{ $t('setting_integration_page.firebase_json_file') }}
            <span class="ml-3">
              <a class="text-primary" href="https://console.firebase.google.com/">Download JSON File</a>
            </span>
          </label>
          <div class="custom-file">
            <input type="file" class="form-control" id="json_file" name="json_file" ref="refInput" accept=".json" @change="fileUpload" />
            <label id="additionalFileHelp" class="custom-file-label upload-label border-0">Upload Firebase JSON files only Once.</label>
            <small class="help-block with-errors text-danger"></small>
          </div>
        </div>
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>
<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
const fileUpload = (e) => {
  let files = e.target.files
  json_file.value = files[0]
}
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      is_google_login: data.is_google_login || 0,
      is_custom_webhook_notification: data.is_custom_webhook_notification || 0,
      is_map_key: data.is_map_key  || 0,
      isForceUpdate: data.isForceUpdate  || 0,
      google_secretkey: data.google_secretkey || '',
      google_publickey: data.google_publickey || '',
      google_maps_key: data.google_maps_key  || '',
      version_code: data.version_code  || '',
      is_application_link: data.is_application_link  || '',
      customer_app_play_store: data.customer_app_play_store  || '',
      customer_app_app_store: data.customer_app_app_store  || '',
      custom_webhook_content_key: data.custom_webhook_content_key  || '',
      custom_webhook_url: data.custom_webhook_url  || '',
      firebase_notification: data.firebase_notification || 0,
      firebase_project_id: data.firebase_project_id || '',
      json_file: data.json_file
    }
  })
}
//validation
const validationSchema = yup.object({
  google_secretkey: yup.string().test('google_secretkey' , 'Must be a valid Google key', function(value){
    if(this.parent.is_google_login == '1' && !value) {
      return false;
    }
    return true
  }),
  google_publickey: yup.string().test('google_publickey', 'Must be a valid Google Publickey', function(value){
    if(this.parent.is_google_login == '1' && !value) {
      return false;
    }
    return true
  }),

  google_maps_key: yup.string().test('google_maps_key', "Must be a valid Google Maps Key", function(value) {
    if(this.parent.is_map_key == '1' && !value) {
      return false;
    }
    return true
  }),
  version_code: yup.string().test('version_code', "Minimum version code for Android is Required", function(value) {
    if(this.parent.isForceUpdate == '1' && !value) {
      return false;
    }
    return true
  }),
  customer_app_play_store: yup.string().test('customer_app_play_store',"Must be a valid Playstore App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  customer_app_app_store: yup.string().test('customer_app_app_store',"Must be a valid App Key", function(value) {
    if(this.parent.is_application_link == '1' && !value) {
      return false;
    }
    return true
  }),
  custom_webhook_content_key: yup.string().test('custom_webhook_content_key',"Must be a valid wbhook content key", function(value) {
    if(this.parent.is_custom_webhook_notification == '1' && !value) {
      return false;
    }
    return true
  }),
  custom_webhook_url: yup.string().test('custom_webhook_url',"Must be a valid wbhook URL", function(value) {
    if(this.parent.is_custom_webhook_notification == '1' &&  !value) {
      return false;
    }
    return true
  }),
  firebase_project_id: yup.string().test('firebase_project_id','Must be a valid Firebase Key', function(value) {
    if(this.parent.firebase_notification == '1' && !value) {
      return false;
    }
    return true
  }),
})
const { handleSubmit, errors, resetForm, validate } = useForm({validationSchema})
const errorMessages = ref({})
const { value: is_google_login } = useField('is_google_login')
const { value: is_custom_webhook_notification } = useField('is_custom_webhook_notification')
const { value: google_secretkey } = useField('google_secretkey')
const { value: google_publickey } = useField('google_publickey')
const { value: is_map_key } = useField('is_map_key')
const { value: isForceUpdate } = useField('isForceUpdate')
const { value: is_application_link } = useField('is_application_link')
const { value: google_maps_key } = useField('google_maps_key')
const { value: version_code } = useField('version_code')
const { value: customer_app_play_store } = useField('customer_app_play_store')
const { value: customer_app_app_store } = useField('customer_app_app_store')
const { value: custom_webhook_content_key} = useField('custom_webhook_content_key')
const { value: custom_webhook_url } = useField('custom_webhook_url')
const { value: firebase_notification } = useField('firebase_notification')
const { value: firebase_project_id } = useField('firebase_project_id')
const { value: json_file } = useField('json_file')
watch(() => is_map_key.value, (value) => {
  if(value == '0') {
    google_maps_key.value = ''
  }
}, {deep: true})
watch(() => isForceUpdate.value, (value) => {
  if(value == '0') {
    version_code.value = ''
  }
}, {deep: true})
watch(() => is_google_login.value, (value) => {
  if(value == '0') {
    google_secretkey.value = ''
    google_publickey.value = ''
  }
}, {deep: true})
watch(() => is_custom_webhook_notification.value, (value) => {
  if(value == '0') {
    custom_webhook_content_key.value = ''
    custom_webhook_url.value = ''
  }
}, {deep: true})
watch(() => firebase_notification.value, (value) => {
  if(value == '0') {
    firebase_project_id.value = firebase_project_id ?? null
  }
}, {deep: true})
// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.errors
  }
}

//fetch data
const data = [
  'is_google_login',
  'firebase_notification',
  'is_mobile_notification',
  'is_map_key',
  'isForceUpdate',
  'is_application_link',
  'is_custom_webhook_notification',
]

const firebase = [
  'firebase_project_id',
  'json_file'
]

const custom_webhook_key = [
  'custom_webhook_content_key',
  'custom_webhook_url'
]

const customer_app = [
  'customer_app_play_store',
  'customer_app_app_store',
]
const google_map_key = [
  'google_maps_key',
]
const versions_key = [
  'version_code',
]
const google_login_key = [
  'google_secretkey',
  'google_publickey',
]
onMounted(() => {

  const customData = [
    ...data,
    ...firebase,
    ...custom_webhook_key,
    ...customer_app,
    ...google_map_key,
    ...versions_key,
    ...google_login_key
  ].join(",")

  createRequest(GET_URL(customData)).then((response) => {
    setFormData(response)
  })
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  const formData = new FormData()
  Object.keys(values).forEach((key) => {
    if (values[key] !== '') {
      formData[key] = values[key] || ''
    }
  })
  if (json_file.value) {
    formData.append('json_file', json_file.value)
  }
  storeRequest({
    url: STORE_URL,
    body: formData,
    type: 'file'
  }).then((res) => display_submit_message(res))
})

defineProps({
  label: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  errorMessage: { type: String, default: '' },
  errorMessages: { type: Array, default: () => [] }
})
</script>
