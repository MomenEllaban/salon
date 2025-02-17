<template>
    <!-- Modal -->
    <form @submit="formSubmit" class="">
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">{{ $t('employee.lbl_create_manager') }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="form-offcanvas">
                          <InputField class="col-md-6" :is-required="true" :label="$t('customer.lbl_first_name')" :placeholder="$t('employee.first_name')" v-model="first_name" :error-message="errors['first_name']" :error-messages="errorMessages['first_name']"></InputField>
                          <InputField class="col-md-6" :is-required="true" :label="$t('customer.lbl_last_name')" :placeholder="$t('employee.last_name')" v-model="last_name" :error-message="errors['last_name']" :error-messages="errorMessages['last_name']"></InputField>

                          <InputField class="col-md-6" :is-required="true" :label="$t('customer.lbl_Email')" :placeholder="$t('customer.lbl_Email')" v-model="email" :error-message="errors['email']" :error-messages="errorMessages['email']"></InputField>
                          <div class="form-group col-md-6">
                            <label class="form-label">{{ $t('branch.lbl_contact_number') }}<span class="text-danger">*</span> </label>
                            <vue-tel-input :value="mobile" @input="handleInput" v-bind="{mode: 'international',maxLen: 15}"></vue-tel-input>
                            <span class="text-danger">{{ errors['mobile'] }}</span>
                          </div>
                            <InputField type="password" class="col-md-6" :is-required="true" :label="$t('employee.lbl_password')" :placeholder="$t('employee.lbl_password')" v-model="password" :error-message="errors['password']" :error-messages="errorMessages['password']"></InputField>

                            <InputField type="password" class="col-md-6" :is-required="true" :label="$t('employee.lbl_confirm_password')" :placeholder="$t('employee.lbl_confirm_password')" v-model="confirm_password" :error-message="errors['confirm_password']" :error-messages="errorMessages['passwconfirm_passwordord']"></InputField>
                            <div class="form-group col-md-12">
                              <label for="" class="w-100">{{ $t('customer.lbl_gender') }}</label>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" v-model="gender" id="male" value="male">
                                  <label class="form-check-label" for="male">
                                    {{ $t('messages.male') }}
                                  </label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" v-model="gender" id="female" value="female">
                                  <label class="form-check-label" for="female">
                                    {{ $t('messages.female') }}
                                  </label>
                                </div>

                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="gender" v-model="gender" id="other" value="other">
                                  <label class="form-check-label" for="other">
                                    {{ $t('messages.intersex') }}
                                  </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">{{ $t('messages.add_manager') }}</button>
                        <button type="button" class="btn btn-outline-primary d-block"  @click="resetform()"  data-bs-dismiss="modal"><i class="fa-solid fa-angles-left"></i>{{ $t('messages.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
<script setup>
import { ref, onMounted } from 'vue'

import { useRequest } from '@/helpers/hooks/useCrudOpration'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useField, useForm } from 'vee-validate'
import { VueTelInput } from 'vue3-tel-input'
import * as yup from 'yup'

import { EMPLOYEE_STORE ,EMAIL_UNIQUE_CHECK } from '@/vue/constants/users'

const emit = defineEmits(['submit'])

const { storeRequest } = useRequest()


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
    password: '',
    confirm_password: '',
    gender: 'male',
    show_in_calender: 1,
    is_manager: 1,
    confirmed: 1
  }
}

onMounted(() => {
  setFormData(defaultData())
})

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      mobile: data.mobile,
      password: data.password,
      confirm_password: data.confirm_password,
      gender: data.gender,
      show_in_calender: data.show_in_calender,
      is_manager: data.is_manager,
      show_in_calender: data.show_in_calender,
      is_manager: data.is_manager,
      confirmed: data.confirmed,
    }
  })
}

let EMAIL_REGX = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;

// Validations
const validationSchema = yup.object({
    first_name: yup.string().required('First Name is required'),
    last_name: yup.string().required('Last Name is required'),
    email: yup.string().required('Email is required')
    .matches(EMAIL_REGX, 'Must be a valid email')
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
        }),
        mobile: yup.string().required('Contact Number is a required field')
        .matches(/^(\+?\d+)?(\s?\d+)*$/, 'Phone Number must contain only digits'),
    password : yup.string().required('Password is required')
    .min(8, 'Password must be at least 8 characters long')
    .max(12, 'Password must be at most 12 characters long'),
      confirm_password : yup.string().required('Current password is required')
      .oneOf([yup.ref('password')], 'Passwords must match')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: first_name } = useField('first_name')
const { value: id } = useField('id')
const { value: last_name } = useField('last_name')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: confirm_password } = useField('confirm_password')
const { value: gender } = useField('gender')
const { value: mobile } = useField('mobile')
const { value: show_in_calender } = useField('show_in_calender')
const { value: is_manager } = useField('is_manager')
const { value: confirmed } = useField('confirmed')
confirmed.value = 1
show_in_calender.value = 1
is_manager.value = 1
const errorMessages = ref({})

// phone number
const handleInput = (phone, phoneObject) => {
  // Handle the input event
  if (phoneObject?.formatted) {
    mobile.value = phoneObject.formatted
  }
};

const resetform = () => {

  setFormData(defaultData())
      bootstrap.Modal.getInstance(document.getElementById("exampleModal")).hide()

};

const formSubmit = handleSubmit((value) => {
  storeRequest({ url: EMPLOYEE_STORE, body: value }).then((res) => {
    if(res.status) {
      emit('submit', {type: 'create_manager', value: res.data.id});
      setFormData(defaultData());
      bootstrap.Modal.getInstance(document.getElementById("exampleModal")).hide();
      successSnackbar(res.message);  
    } else {
      errorSnackbar(res.message); 
      errorMessages.value = res.all_message;
    }
  }).catch((error) => {
    errorSnackbar("An error occurred during submission");  
    console.error(error);
  });
});


</script>
