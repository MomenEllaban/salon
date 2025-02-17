<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12 row">
            <div class="col-md-8">
              <div class="row">
                <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_first_name')" :placeholder="$t('employee.first_name')"
                  v-model="first_name" :error-message="errors['first_name']"
                  :error-messages="errorMessages['first_name']"></InputField>
                <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_last_name')" :placeholder="$t('employee.last_name')"
                  v-model="last_name" :error-message="errors['last_name']" :error-messages="errorMessages['last_name']" autocomplete="off">
                </InputField>

                <InputField class="col-md-6" :is-required="true" :label="$t('employee.lbl_Email')" :placeholder="$t('employee.email_address')"
                  v-model="email" :error-message="errors['email']" :error-messages="errorMessages['email']" ></InputField>
                <div class="form-group col-md-6">
                  <label class="form-label">{{ $t('employee.lbl_phone_number') }}<span class="text-danger">*</span>
                  </label>
                  <vue-tel-input :value="mobile" @input="handleInput"
                    v-bind="{ mode: 'international', maxLen: 15 }" autocomplete="new-password"></vue-tel-input>
                  <span class="text-danger">{{ errors['mobile'] }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-center">
              <img :src="ImageViewer || defaultImage" class="img-fluid avatar avatar-120 avatar-rounded mb-2"
                alt="profile-image" />
                <div v-if="validationMessage" class="text-danger mb-2">{{ validationMessage }}</div>
              <div class="d-flex align-items-center justify-content-center gap-2">
                <input type="file" ref="profileInputRef" class="form-control d-none" id="logo" name="profile_image"
                  accept=".jpeg, .jpg, .png, .gif" @change="fileUpload" />
                <label class="btn btn-info" for="logo">{{ $t('messages.upload') }}</label>
                <input type="button" class="btn btn-danger" name="remove" value="Remove" @click="removeLogo()"
                  v-if="ImageViewer" />
              </div>
              <span class="text-danger">{{ errors.profile_image }}</span>
            </div>
            <div class="row" v-if="currentId === 0">
              <InputField type="password" class="col-md-6" :is-required="true" :label="$t('employee.lbl_password')"
                :placeholder="$t('employee.password')" v-model="password" :error-message="errors['password']" :autocomplete="newpassword"
                :error-messages="errorMessages['password']"></InputField>

              <InputField type="password" class="col-md-6" :is-required="true" :label="$t('employee.lbl_confirm_password')"
                :placeholder="$t('employee.confirm_password')" v-model="confirm_password" :error-message="errors['confirm_password']"
                :error-messages="errorMessages['confirm_password']"></InputField>
            </div>

            <div class="form-group col-md-4">
              <label for="" class="w-100">{{ $t('employee.lbl_gender') }}</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male"
                  :checked="gender == 'male'" />
                <label class="form-check-label" for="male"> Male </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female"
                  :checked="gender == 'female'" />
                <label class="form-check-label" for="female"> Female </label>
              </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other"
                  :checked="gender == 'other'" />
                <label class="form-check-label" for="other"> Intersex </label>
              </div>
              <p class="mb-0 text-danger">{{ errors.gender }}</p>
            </div>

            <div class="form-group m-0 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0"
                  v-model="show_in_calender" id="show-in-calender" :checked="show_in_calender">
                <label class="form-check-label" for="show-in-calender">
                  {{ $t('employee.lbl_show_in_calender') }}
                </label>
              </div>
            </div>

            <div class="form-group m-0 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" :true-value="1" :false-value="0" v-model="is_manager"
                  id="is-manager" :checked="is_manager">
                <label class="form-check-label" for="is-manager">
                  {{ $t('employee.lbl_is_manager') }}
                </label>
              </div>
            </div>
            <div class="form-group col-md-12" v-if="branch.options.length > 1 && selectedSessionBranchId == ''">
              <label class="form-label" for="branch">{{ $t('employee.lbl_select_branch') }}</label><span class="text-danger">*</span>
              <Multiselect id="branch_id" v-model="branch_id" :value="branch_id" placeholder="Select Branch"
                v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" class="form-group">
              </Multiselect>
              <span v-if="errorMessages['branch_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['branch_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.branch_id }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="service">{{ $t('employee.lbl_select_service') }}</label>
              <Multiselect id="service_id" v-model="service_id" :multiple="true" :value="service_id"
                placeholder="Select Service" v-bind="multiSelectOption" :options="services.options" class="form-group">
              </Multiselect>
              <span v-if="errorMessages['service_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['service_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.service_id }}</span>
            </div>

            <div class="form-group col-md-12">
              <label class="form-label" for="commission_id"> {{ $t('employee.lbl_select_commission') }} <span class="text-danger">*</span> </label>
              <Multiselect id="commission_id" v-model="commission_id" :value="commission_id"
                placeholder="Select Commission" v-bind="singleSelectOption" :options="commissions.options"
                class="form-group"></Multiselect>
              <span v-if="errorMessages['commission_id']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['commission_id']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.commission_id }}</span>
            </div>

            <div v-for="field in customefield" :key="field.id">

              <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type"
                :required="field.required" :options="field.value" :field_id="field.id"></FormElement>

            </div>


            <InputField class="col-md-6" :label="$t('employee.lbl_about_self')" :placeholder="$t('employee.about_self')" v-model="about_self"
              :error-message="errors['about_self']" :error-messages="errorMessages['about_self']"></InputField>
            <InputField class="col-md-6" :label="$t('employee.lbl_expert')" :placeholder="$t('employee.expert')" v-model="expert"
              :error-message="errors['expert']" :error-messages="errorMessages['expert']"></InputField>
            <InputField class="col-md-6" :label="$t('employee.lbl_facebook_link')" :placeholder="$t('employee.facebook_link')" v-model="facebook_link"
              :error-message="errors['facebook_link']" :error-messages="errorMessages['facebook_link']"></InputField>
            <InputField class="col-md-6" :label="$t('employee.lbl_instagram_link')" :placeholder="$t('employee.instagram_link')"
              v-model="instagram_link" :error-message="errors['instagram_link']"
              :error-messages="errorMessages['instagram_link']"></InputField>
            <InputField class="col-md-6" :label="$t('employee.lbl_twitter_link')" :placeholder="$t('employee.Twitter_link')" v-model="twitter_link"
              :error-message="errors['twitter_link']" :error-messages="errorMessages['twitter_link']"></InputField>
            <InputField class="col-md-6" :label="$t('employee.lbl_dribbble_link')" :placeholder="$t('employee.dribble_link')" v-model="dribbble_link"
              :error-message="errors['dribbble_link']" :error-messages="errorMessages['dribbble_link']"></InputField>
            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('employee.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox"
                    v-model="status" />
                </div>
              </div>
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
import { EDIT_URL, STORE_URL, UPDATE_URL, BRANCH_LIST, SERVICE_LIST, COMMISSION_LIST, EMAIL_UNIQUE_CHECK } from '../constant/employee'
import { useField, useForm } from 'vee-validate'

import { VueTelInput } from 'vue3-tel-input'

import { useModuleId, useRequest, useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'

import { readFile } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'

// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  defaultImage: { type: String, default: 'https://dummyimage.com/600x300/cfcfcf/000000.png' },
  customefield: { type: Array, default: () => [] },
  selectedSessionBranchId: {type: Number, default: null}
})

// Select Options
const singleSelectOption = ref({
  closeOnSelect: true,
  searchable: true
})
const multiSelectOption = ref({
  mode: 'tags',
  closeOnSelect: true,
  searchable: true,
})

const { getRequest, storeRequest, updateRequest } = useRequest()

// Edit Form Or Create Form
const currentId = useModuleId(() => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
    if(props.selectedSessionBranchId !== null) {
      branch_id.value = props.selectedSessionBranchId
    } else if (data.options.length === 1) {
      branch_id.value = data.options[0].value
      branchSelect()
    }
    branchSelect()
  })
  useSelect({ url: COMMISSION_LIST }, { value: 'id', label: 'name' }).then((data) => (commissions.value = data))
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status && res.data) {
        setFormData(res.data)
        branchSelect()
      }
    })
  } else {
    setFormData(defaultData())
  }
})

const branch = ref({ options: [], list: [] })
const commissions = ref({ options: [], list: [] })
const services = ref({ options: [], list: [] })

onMounted(() => {
  setFormData(defaultData())
})

const branchSelect = () => {
  useSelect({ url: SERVICE_LIST, data: { branch_id: branch_id.value } }, { value: 'id', label: 'name' }).then((data) => (services.value = data))
}

// File Upload Function
const ImageViewer = ref(null)
const validationMessage = ref('');
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
    profile_image.value = file;
  } else {
    validationMessage.value = '';
  }
};

// Function to delete Images
const removeImage = ({ imageViewerBS64, changeFile }) => {
  imageViewerBS64.value = null
  changeFile.value = null
}

// const changeLogo = (e) => fileUpload(e, { imageViewerBS64: ImageViewer, changeFile: profile_image })
const removeLogo = () => removeImage({ imageViewerBS64: ImageViewer, changeFile: profile_image })



/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    id: '',
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    password: '',
    confirm_password: '',
    gender: 'male',
    password: '',
    profile_image: '',
    status: 1,
    branch_id: 0,
    service_id: [],
    commission_id: '',
    show_in_calender: 1,
    is_manager: 0,
    about_self: '',
    expert: '',
    facebook_link: '',
    instagram_link: '',
    twitter_link: '',
    dribbble_link: '',
    custom_fields_data: {
    }
  }
}



//  Reset Form
const setFormData = (data) => {
  ImageViewer.value = data.profile_image
  resetForm({
    values: {
      id: data.id,
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      password: data.password,
      confirm_password: data.confirm_password,
      gender: data.gender,
      profile_image: data.profile_image,
      branch_id: data.branch_id,
      service_id: data.service_id,
      commission_id: data.commission_id,
      status: data.status ? true : false,
      show_in_calender: data.show_in_calender,
      is_manager: data.is_manager,
      custom_fields_data: data.custom_field_data,
      about_self: data.about_self,
      expert: data.expert,
      facebook_link: data.facebook_link,
      instagram_link: data.instagram_link,
      twitter_link: data.twitter_link,
      dribbble_link: data.dribbble_link,
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
const numberRegex = /^\d+$/;
const EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
// Validations

const validationSchema = yup.object({
    first_name: yup.string()
      .required('First name is a required field')
      .test('is-string', 'Only strings are allowed', (value) => {
        // Regular expressions to disallow special characters and numbers
        const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/;
        return !specialCharsRegex.test(value) && !numberRegex.test(value);
    }),
    last_name: yup.string()
      .required('Last name is a required field')
      .test('is-string', 'Only strings are allowed', (value) => {
        // Regular expressions to disallow special characters and numbers
        const specialCharsRegex = /[!@#$%^&*(),.?":{}|<>\-_;'\/+=\[\]\\]/;
        return !specialCharsRegex.test(value) && !numberRegex.test(value);
      }),
    email: yup.string().required('Email is a required field')
    .test('is-string', 'First strings are allowed', (value) => !numberRegex
    .test(value))
    .test('unique', 'Email must be unique', async function(value) {
      if (!EMAIL_REGX.test(value)) {
        return true;
      }
      const userId  = id.value;
      console.log(userId)
          const isUnique = await storeRequest({ url: EMAIL_UNIQUE_CHECK, body: { email: value, user_id: userId }, type: 'file' });
          if (!isUnique.isUnique) {
              return this.createError({ path: 'email', message: 'email must be unique' });
              }
          return true;
        })
    .matches(EMAIL_REGX, 'Must be a valid email'),
    mobile: yup.string()
      .required('Phone Number is a required field').matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
    password : yup.string().test('password','Password is required' , function(value) {
      if(currentId === 0 && !value){
        return false;
      }
      return true
    }
    ).min(8, 'Password must be at least 8 characters long'),
    confirm_password : yup.string().test('confirm_password', 'Current password is required', function(value) {
      if(currentId === 0 && !value){
        return false;
      }
      return true
    }
    ).oneOf([yup.ref('password')], 'Passwords must match'),
    commission_id: yup.string()
      .required('Select commission is a required field'),
      branch_id: yup.string()
      .required('Select Branch is a required field'),
});



const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})
const { value: id } = useField('id')
const { value: first_name } = useField('first_name')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const { value: gender } = useField('gender')
const { value: mobile } = useField('mobile')
const { value: branch_id } = useField('branch_id')
const { value: status } = useField('status')
const { value: service_id } = useField('service_id')
const { value: commission_id } = useField('commission_id')
const { value: profile_image } = useField('profile_image')
const { value: show_in_calender } = useField('show_in_calender')
const { value: is_manager } = useField('is_manager')
const { value: custom_fields_data } = useField('custom_fields_data')
const { value: about_self } = useField('about_self')
const { value: expert } = useField('expert')
const { value: facebook_link } = useField('facebook_link')
const { value: instagram_link } = useField('instagram_link')
const { value: twitter_link } = useField('twitter_link')
const { value: dribbble_link } = useField('dribbble_link')

const errorMessages = ref({})

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
};

// Form Submit
const IS_SUBMITED = ref(false)

const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false
  IS_SUBMITED.value = true;
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)
  console.log("submit",values);
  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
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
