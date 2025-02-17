<template>
  <form @submit="formSubmit">
    <div class="d-flex justify-content-between align-items-center">
      <CardTitle :title="$t('setting_sidebar.lbl_General')" icon="fas fa-cube"></CardTitle>
      <a type="submit" class="btn btn-primary float-right" @click="clearcache()"><i class="fa-solid fa-arrow-rotate-left mx-2"></i>{{ $t('settings.purge_cache')}}</a>
    </div>
    <InputField :label="$t('setting_general_page.lbl_app')" :value="app_name" v-model="app_name" :errorMessage="errors.app_name"></InputField>

    <InputField :label="$t('setting_general_page.lbl_footer')" :value="footer_text" v-model="footer_text" :errorMessage="errors.footer_text"></InputField>
    <InputField :label="$t('setting_general_page.lbl_copyright')" :value="copyright_text" v-model="copyright_text" :errorMessage="errors.copyright_text"></InputField>
    <InputField :label="$t('setting_general_page.lbl_uitext')" :value="ui_text" v-model="ui_text" :errorMessage="errors.ui_text"></InputField>
    <InputField :label="$t('setting_general_page.lbl_contact_no')" :value="helpline_number" v-model="helpline_number" :errorMessage="errors.helpline_number"></InputField>

    <InputField :label="$t('setting_general_page.lbl_inquiry_email')" :value="inquriy_email" v-model="inquriy_email" :errorMessage="errors.inquriy_email"></InputField>
    <InputField :label="$t('setting_general_page.lbl_site_description')" :value="site_description" v-model="site_description" :errorMessage="errors.site_description"></InputField>

    <div class="row">
      <h5 class="mb-3">{{ $t('setting_general_page.business_add') }}</h5>
      <InputField class="col-md-6" :is-required="false" :label="$t('branch.lbl_shop_number')" placeholder="" v-model="bussiness_address_line_1"></InputField>
      <InputField class="col-md-6" :is-required="false" :label="$t('branch.lbl_landmark')" placeholder="" v-model="bussiness_address_line_2"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_country')" placeholder="" v-model="bussiness_address_country"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_state')" placeholder="" v-model="bussiness_address_state"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_city')" placeholder="" v-model="bussiness_address_city"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_postal_code')" placeholder="" v-model="bussiness_address_postal_code"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_lat')" placeholder="" v-model="bussiness_address_latitude"></InputField>
      <InputField class="col-md-2" :is-required="false" :label="$t('branch.lbl_long')" placeholder="" v-model="bussiness_address_longitude"></InputField>
    </div>

    <div class="col row">
      <h5 class="mb-3">{{ $t('setting_general_page.branding') }}</h5>
      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body">
                <img :src="logoViewer || DEFAULT_LOGO" class="img-fluid" alt="logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="logoInputRef" class="form-control d-none" id="logo" name="logo" accept=".jpeg, .jpg, .png, .gif" @change="changeLogo" />
              <label class="btn btn-primary mb-5" for="logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeLogo()" v-if="logo" />
            </div>
            <span class="text-danger">{{ errors.logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.mini_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body">
                <img :src="miniLogoViewer || DEFAULT_MINI_LOGO" class="img-fluid" alt="mini_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="logoMiniInputRef" class="form-control d-none" id="mini-logo" name="mini_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeMiniLogo" />
              <label class="btn btn-primary mb-5" for="mini-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeMiniLogo()" v-if="mini_logo" />
            </div>
            <span class="text-danger">{{ errors.mini_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.dark_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body bg-dark">
                <img :src="darkLogoViewer || DEFAULT_DARK_LOGO" class="img-fluid" alt="dark_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="darkLogoInputRef" class="form-control d-none" id="dark-logo" name="dark_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeDarkLogo" />
              <label class="btn btn-primary mb-5" for="dark-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeDarkLogo()" v-if="dark_logo" />
            </div>
            <span class="text-danger">{{ errors.dark_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.dark-mini_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center inline-block">
              <div class="card-body bg-dark">
                <img :src="darkMiniLogoViewer || DEFAULT_DARK_MINI_LOGO" class="img-fluid" alt="dark_mini_logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="darkLogoMiniInputRef" class="form-control d-none" id="dark-mini-logo" name="dark_mini_logo" accept=".jpeg, .jpg, .png, .gif" @change="changeDarkMiniLogo" />
              <label class="btn btn-primary mb-5" for="dark-mini-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5 mb-5" name="remove" value="Remove" @click="removeDarkMiniLogo()" v-if="dark_mini_logo" />
            </div>
            <span class="text-danger">{{ errors.dark_mini_logo }}</span>
          </div>
        </div>
      </div>

      <div class="form-group mb-3 col-md-6">
        <label for="logo" class="form-label">{{ $t('messages.favicon') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4 r-0">
            <div class="card text-center inline-block">
              <div class="card-body">
                <img :src="faviconViewer || DEFAULT_FAVICON" class="img-fluid favicon-image" alt="favicon" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" ref="faviconInputRef" class="form-control d-none" id="favicon-logo" name="favicon" accept=".jpeg, .jpg, .png, .gif" @change="changeFavicon" />
              <label class="btn btn-primary mb-5" for="favicon-logo">{{ $t('messages.upload') }}</label>
              <input type="button" class="btn btn-danger mb-5" name="remove" value="Remove" @click="removeFavicon()" v-if="favicon" />
            </div>
            <span class="text-danger">{{ errors.favicon }}</span>
          </div>
        </div>
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import CardTitle from '@/Setting/Components/CardTitle.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { onMounted, ref } from 'vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL, CACHE_CLEAR } from '@/vue/constants/setting'

import { readFile } from '@/helpers/utilities'
import { createRequest } from '@/helpers/utilities'
import * as yup from 'yup'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import SubmitButton from './Forms/SubmitButton.vue'

const DEFAULT_LOGO = document.querySelector('[name="logo"]').value
const DEFAULT_MINI_LOGO = document.querySelector('[name="mini-logo"]').value
const DEFAULT_DARK_LOGO = document.querySelector('[name="dark-logo"]').value
const DEFAULT_DARK_MINI_LOGO = document.querySelector('[name="dark-mini-logo"]').value
const DEFAULT_FAVICON = document.querySelector('[name="favicon"]').value
const IS_SUBMITED = ref(false)

const { storeRequest,deleteRequest  } = useRequest()

let logoInputRef = ref(null)
let logoMiniInputRef = ref(null)
let darkLogoInputRef = ref(null)
let darkLogoMiniInputRef = ref(null)
let faviconInputRef = ref(null)

const fileUpload = async (e, { imageViewerBS64, changeFile }) => {
  let file = e.target.files[0]
  await readFile(file, (fileB64) => {
    imageViewerBS64.value = fileB64

    logoInputRef.value.value = ''
    logoMiniInputRef.value.value = ''
    darkLogoInputRef.value.value = ''
    darkLogoMiniInputRef.value.value = ''
    faviconInputRef.value.value = ''
  })
  changeFile.value = file
}
// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

const logoViewer = ref(DEFAULT_LOGO)
const changeLogo = (e) => fileUpload(e, { imageViewerBS64: logoViewer, changeFile: logo })
const removeLogo = () => removeImage({ imageViewerBS64: logoViewer, changeFile: logo })

const miniLogoViewer = ref(DEFAULT_MINI_LOGO)
const changeMiniLogo = (e) => fileUpload(e, { imageViewerBS64: miniLogoViewer, changeFile: mini_logo })
const removeMiniLogo = () => removeImage({ imageViewerBS64: miniLogoViewer, changeFile: mini_logo })

const darkLogoViewer = ref(DEFAULT_DARK_LOGO)
const changeDarkLogo = (e) => fileUpload(e, { imageViewerBS64: darkLogoViewer, changeFile: dark_logo })
const removeDarkLogo = () => removeImage({ imageViewerBS64: darkLogoViewer, changeFile: dark_logo })

const darkMiniLogoViewer = ref(DEFAULT_DARK_MINI_LOGO)
const changeDarkMiniLogo = (e) => fileUpload(e, { imageViewerBS64: darkMiniLogoViewer, changeFile: dark_mini_logo })
const removeDarkMiniLogo = () => removeImage({ imageViewerBS64: darkMiniLogoViewer, changeFile: dark_mini_logo })

const faviconViewer = ref(DEFAULT_FAVICON)
const changeFavicon = (e) => fileUpload(e, { imageViewerBS64: faviconViewer, changeFile: favicon })
const removeFavicon = () => removeImage({ imageViewerBS64: faviconViewer, changeFile: favicon })

//  Reset Form
const setFormData = (data) => {
  ;(logoViewer.value = data.logo),
    (miniLogoViewer.value = data.mini_logo),
    (darkLogoViewer.value = data.dark_logo),
    (darkMiniLogoViewer.value = data.dark_mini_logo),
    (faviconViewer.value = data.favicon),
    resetForm({
      values: {
        app_name: data.app_name,
        footer_text: data.footer_text,
        ui_text: data.ui_text,
        copyright_text: data.copyright_text,
        helpline_number: data.helpline_number,
        inquriy_email: data.inquriy_email,
        site_description: data.site_description,
        logo: '',
        mini_logo: '',
        dark_logo: '',
        dark_mini_logo: '',
        favicon: '',
        bussiness_address_line_1: data.bussiness_address_line_1,
        bussiness_address_line_2: data.bussiness_address_line_2,
        bussiness_address_country: data.bussiness_address_country,
        bussiness_address_state: data.bussiness_address_state,
        bussiness_address_city: data.bussiness_address_city,
        bussiness_address_postal_code: data.bussiness_address_postal_code,
        bussiness_address_latitude: data.bussiness_address_latitude,
        bussiness_address_longitude: data.bussiness_address_longitude
      }
    })
}

const numberRegex = /^\d+$/

const validationSchema = yup.object({
  app_name: yup.string().required('App name is required'),
  footer_text: yup.string().required('Footer text is required'),
  helpline_number: yup.string().matches(/^\d+$/, 'Only numbers are allowed'),
  copyright_text: yup.string().required('Copyright text is required'),
  inquriy_email: yup
    .string()
    .test('is-string', 'First strings are allowed', (value) => !numberRegex.test(value))
    .email('must be a valid email'),
  site_description: yup.string()
})

const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const errorMessage = ref(null)
const { value: app_name } = useField('app_name')
const { value: ui_text } = useField('ui_text')
const { value: footer_text } = useField('footer_text')
const { value: helpline_number } = useField('helpline_number')
const { value: copyright_text } = useField('copyright_text')
const { value: inquriy_email } = useField('inquriy_email')
const { value: site_description } = useField('site_description')
const { value: logo } = useField('logo')
const { value: mini_logo } = useField('mini_logo')
const { value: dark_logo } = useField('dark_logo')
const { value: dark_mini_logo } = useField('dark_mini_logo')
const { value: favicon } = useField('favicon')
const { value: bussiness_address_line_1 } = useField('bussiness_address_line_1')
const { value: bussiness_address_line_2 } = useField('bussiness_address_line_2')
const { value: bussiness_address_country } = useField('bussiness_address_country')
const { value: bussiness_address_state } = useField('bussiness_address_state')
const { value: bussiness_address_city } = useField('bussiness_address_city')
const { value: bussiness_address_postal_code } = useField('bussiness_address_postal_code')
const { value: bussiness_address_latitude } = useField('bussiness_address_latitude')
const { value: bussiness_address_longitude } = useField('bussiness_address_longitude')
//fetch data
const data = 'app_name,footer_text,helpline_number,copyright_text,inquriy_email,site_description,logo,mini_logo,dark_logo,dark_mini_logo,favicon,ui_text,bussiness_address_postal_code,bussiness_address_line_1,bussiness_address_line_2,bussiness_address_country,bussiness_address_state,bussiness_address_city,bussiness_address_latitude,bussiness_address_longitude'
onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

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

  const filteredValues = {}
  Object.entries(values).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      filteredValues[key] = value
    }
  })

  IS_SUBMITED.value = true
  storeRequest({ url: STORE_URL, body: filteredValues, type: 'file' }).then((res) => display_submit_message(res))
})

const clearcache = () => {
  deleteRequest({ url: CACHE_CLEAR }).then((res) => {
    if (res.status) {
      display_submit_message(res)
    }
  })
}
</script>

<style>
.favicon-image {
  width: 50px;
  height: 50px;
}
</style>
