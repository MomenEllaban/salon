<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center">
      <CardTitle title="Personal Information" icon="fa-solid fa-user"></CardTitle>
    </div>
    <div class="row">
      <div class="col-12 row">
        <div class="col-md-8">
          <div class="row">
            <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_first_name')" :value="first_name" v-model="first_name" :placeholder="$t('profile.enter_first_name')" :error-message="errors['first_name']"></InputField>
            <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_last_name')" :value="last_name" v-model="last_name" :placeholder="$t('profile.enter_last_name')" :error-message="errors['last_name']"></InputField>
            <InputField class="col-md-6" :is-required="true" :label="$t('profile.lbl_email')" :value="email" v-model="email" :placeholder="$t('profile.enter_email')" :error-message="errors['email']"></InputField>
            <div class="form-group col-md-6">
              <label class="form-label"> {{ $t('profile.lbl_contact_number') }} <span class="text-danger">*</span> </label>
              <vue-tel-input type="number" :value="mobile" @input="handleInput" v-bind="{ mode: 'international', maxLen: 15 }"></vue-tel-input>
              <span class="text-danger">{{ errors['mobile'] }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-4 text-center">
          <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2" alt="profile-image" />
          <div class="d-flex align-items-center justify-content-center gap-2">
            <input type="file" ref="profileInputRef" class="form-control d-none" id="logo" name="profile_image" accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
            <label class="btn btn-info" for="logo">{{ $t('messages.upload') }}</label>
            <input type="button" class="btn btn-danger" name="remove" value="Remove" @click="removeLogo()" v-if="ImageViewer" />
          </div>
          <span class="text-danger">{{ errors.profile_image }}</span>
        </div>
        <div class="form-group col-md-4">
          <label for="" class="w-100">{{ $t('profile.lbl_gender') }}</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male" :checked="gender == 'male'" />
            <label class="form-check-label" for="male">{{ $t('messages.male') }} </label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female" :checked="gender == 'female'" />
            <label class="form-check-label" for="female"> {{ $t('messages.female') }} </label>
          </div>

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other" :checked="gender == 'other'" />
            <label class="form-check-label" for="other">{{ $t('messages.intersex') }} </label>
          </div>
          <p class="mb-0 text-danger">{{ errors.gender }}</p>
        </div>
        <div class="form-group m-0 col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0" v-model="show_in_calender" id="show-in-calender" :checked="show_in_calender" />
            <label class="form-check-label" for="show-in-calender">
              {{ $t('profile.lbl_show_in_calender') }}
            </label>
          </div>
        </div>

        <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
      </div>
    </div>
  </form>
</template>

<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { onMounted, ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { VueTelInput } from 'vue3-tel-input'
import { INFORMATION_STORE, GET_URL } from '@/vue/constants/users'

import { readFile } from '@/helpers/utilities'
import { createRequest } from '@/helpers/utilities'
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import SubmitButton from './Forms/SubmitButton.vue'

const IS_SUBMITED = ref(false)

const { storeRequest } = useRequest()

// File Upload Function
const ImageViewer = ref(null)
const profileInputRef = ref(null)
const defaultImage = ref(window.auth_profile_image)

const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    profileInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const changeLogo = (e) => fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: profile_image })
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: profile_image })

//  Reset Form
const setFormData = (data, defaultImage) => {
  if (data.profile_image === defaultImage) {
    ImageViewer.value = null
  } else {
    ImageViewer.value = data.profile_image
  }
  resetForm({
    values: {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      show_in_calender: data.show_in_calender,
      gender: data.gender,
      profile_image: data.profile_image
    }
  })
}

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
}

const validationSchema = yup.object({
  first_name: yup.string().required('first name is required'),
  last_name: yup.string().required('last name is required'),
  email: yup.string().required('email is required'),
  mobile: yup.string().required('mobile is required'),
  show_in_calender: yup.string().required('show_in_calender is required')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const errorMessages = ref(null)
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: mobile } = useField('mobile')
const { value: show_in_calender } = useField('show_in_calender')
const { value: gender } = useField('gender')
const { value: profile_image } = useField('profile_image')

//fetch data
const data = 'first_name'
onMounted(() => {
  createRequest(GET_URL()).then((response) => {
    if (response.status) {
      defaultImage.value = response.defaultImage
      setFormData(response.data, response.defaultImage)
    }
  })
})

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
    window.location.reload()
  } else {
    window.errorSnackbar(res.message)
    // errorMessages.value = res.all_message
  }
}

//Form Submit
const formSubmit = handleSubmit((values) => {
  IS_SUBMITED.value = true
  storeRequest({ url: INFORMATION_STORE, body: values, type: 'file' }).then((res) => display_submit_message(res))
})
</script>

<style>
.favicon-image {
  width: 50px;
  height: 50px;
}
</style>
