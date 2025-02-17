<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_payment')" icon="fa-solid fa-coins"></CardTitle>
    </div>
    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="payment_method_razorpay">{{ $t('setting_payment_method.lbl_razorpay') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="razor_payment_method"
            :checked="razor_payment_method == 1 ? true : false" name="razor_payment_method" id="payment_method_razorpay"
            type="checkbox" v-model="razor_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="razor_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="razorpay_secretkey" :error-message="errors['razorpay_secretkey']" :error-messages="errorMessages['razorpay_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_app_key')" placeholder="" v-model="razorpay_publickey" :error-message="errors['razorpay_publickey']" :error-messages="errorMessages['razorpay_publickey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="payment_method_stripe">{{ $t('setting_payment_method.lbl_stripe') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="str_payment_method"
            :checked="str_payment_method == 1 ? true : false" name="str_payment_method" id="payment_method_stripe"
            type="checkbox" v-model="str_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="str_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="stripe_secretkey" :error-message="errors['stripe_secretkey']" :error-messages="errorMessages['stripe_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_app_key')" placeholder="" v-model="stripe_publickey" :error-message="errors['stripe_publickey']" :error-messages="errorMessages['stripe_publickey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="payment_method_paystack">{{ $t('setting_payment_method.lbl_paystack') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="paystack_payment_method"
            :checked="paystack_payment_method == 1 ? true : false" name="paystack_payment_method"
            id="payment_method_paystack" type="checkbox" v-model="paystack_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="paystack_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="paystack_secretkey" :error-message="errors['paystack_secretkey']" :error-messages="errorMessages['paystack_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_app_key')" placeholder="" v-model="paystack_publickey" :error-message="errors['paystack_publickey']" :error-messages="errorMessages['paystack_publickey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="payment_method_paypal">{{ $t('setting_payment_method.lbl_paypal') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="paypal_payment_method"
            :checked="paypal_payment_method == 1 ? true : false" name="paypal_payment_method" id="payment_method_paypal"
            type="checkbox" v-model="paypal_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="paypal_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="paypal_secretkey" :error-message="errors['paypal_secretkey']" :error-messages="errorMessages['paypal_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_client_id')" placeholder="" v-model="paypal_clientid" :error-message="errors['paypal_clientid']" :error-messages="errorMessages['paypal_clientid']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="flutterwave_method_paypal">{{ $t('setting_payment_method.lbl_flutterwave')
          }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="flutterwave_payment_method"
            :checked="flutterwave_payment_method == 1 ? true : false" name="flutterwave_payment_method"
            id="flutterwave_method_paypal" type="checkbox" v-model="flutterwave_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="flutterwave_payment_method == 1">
      <div class="row">
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="flutterwave_secretkey" :error-message="errors['flutterwave_secretkey']" :error-messages="errorMessages['flutterwave_secretkey']"></InputField>
        </div>
        <div class="col-md-6">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_app_key')" placeholder="" v-model="flutterwave_publickey" :error-message="errors['flutterwave_publickey']" :error-messages="errorMessages['flutterwave_publickey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="cinet_method">{{ $t('setting_payment_method.lbl_cinet') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="cinet_payment_method"
            :checked="cinet_payment_method == 1 ? true : false" name="cinet_payment_method" id="cinet_method"
            type="checkbox" v-model="cinet_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="cinet_payment_method == 1">
      <div class="row">
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_client_id')" placeholder="" v-model="cinet_clientid" :error-message="errors['cinet_clientid']" :error-messages="errorMessages['cinet_clientid']"></InputField>
        </div>
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_cinet_apikey')" placeholder="" v-model="cinet_apikey" :error-message="errors['cinet_apikey']" :error-messages="errorMessages['cinet_apikey']"></InputField>
        </div>
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="cinet_secretkey" :error-message="errors['cinet_secretkey']" :error-messages="errorMessages['cinet_secretkey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="sadad_method">{{ $t('setting_payment_method.lbl_sadad') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="sadad_payment_method"
            :checked="sadad_payment_method == 1 ? true : false" name="sadad_payment_method" id="sadad_method"
            type="checkbox" v-model="sadad_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="sadad_payment_method == 1">
      <div class="row">
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_client_id')" placeholder="" v-model="sadad_clientid" :error-message="errors['sadad_clientid']" :error-messages="errorMessages['sadad_clientid']"></InputField>
        </div>
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="sadad_secretkey" :error-message="errors['sadad_secretkey']" :error-messages="errorMessages['sadad_secretkey']"></InputField>
        </div>
        <div class="col-md-4">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_sadad_domain')" placeholder="" v-model="sadad_domain" :error-message="errors['sadad_domain']" :error-messages="errorMessages['sadad_domain']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="airtelmoney_method">{{ $t('setting_payment_method.lbl_airtelmoney') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="airtelmoney_payment_method"
            :checked="airtelmoney_payment_method == 1 ? true : false" name="airtelmoney_payment_method"
            id="airtelmoney_method" type="checkbox" v-model="airtelmoney_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="airtelmoney_payment_method == 1">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label class="form-label" for="is_airtelmoney_status_method">{{ $t('setting_payment_method.lbl_is_live') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :true-value="1" :false-value="0" :value="airtelmoney_is_status"
                :checked="airtelmoney_is_status == 1 ? true : false" name="airtelmoney_is_status" id="is_airtelmoney_status_method"
                type="checkbox" v-model="airtelmoney_is_status" />
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_client_id')" placeholder="" v-model="airtelmoney_clientid" :error-message="errors['airtelmoney_clientid']" :error-messages="errorMessages['airtelmoney_clientid']"></InputField>
        </div>
        <div class="col-md-5">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_secret_key')" placeholder="" v-model="airtelmoney_secretkey" :error-message="errors['airtelmoney_secretkey']" :error-messages="errorMessages['airtelmoney_secretkey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="phonepay_method">{{ $t('setting_payment_method.lbl_phonepay') }}</label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="phonepay_payment_method"
            :checked="phonepay_payment_method == 1 ? true : false" name="phonepay_payment_method" id="phonepay_method"
            type="checkbox" v-model="phonepay_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="phonepay_payment_method == 1">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label class="form-label" for="is_phonepay_status_method">{{ $t('setting_payment_method.lbl_is_live') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :true-value="1" :false-value="0" :value="phonepay_is_status"
                :checked="phonepay_is_status == 1 ? true : false" name="phonepay_is_status" id="is_phonepay_status_method"
                type="checkbox" v-model="phonepay_is_status" />
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_app_id')" placeholder="" v-model="phonepay_appid" :error-message="errors['phonepay_appid']" :error-messages="errorMessages['phonepay_appid']"></InputField>
        </div>
        <div class="col-md-3">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_merchentid')" placeholder="" v-model="phonepay_merchentid" :error-message="errors['phonepay_merchentid']" :error-messages="errorMessages['phonepay_merchentid']"></InputField>
        </div>
        <div class="col-md-3">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_saltid')" placeholder="" v-model="phonepay_saltid" :error-message="errors['phonepay_saltid']" :error-messages="errorMessages['phonepay_saltid']"></InputField>
        </div>
        <div class="col-md-3">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_saltkey')" placeholder="" v-model="phonepay_saltkey" :error-message="errors['phonepay_saltkey']" :error-messages="errorMessages['phonepay_saltkey']"></InputField>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="d-flex justify-content-between align-items-center">
        <label class="form-label" for="midtrans_method">{{ $t('setting_payment_method.lbl_midtrans') }} </label>
        <div class="form-check form-switch">
          <input class="form-check-input" :true-value="1" :false-value="0" :value="midtrans_payment_method"
            :checked="midtrans_payment_method == 1 ? true : false" name="midtrans_payment_method" id="midtrans_method"
            type="checkbox" v-model="midtrans_payment_method" />
        </div>
      </div>
    </div>
    <div v-if="midtrans_payment_method == 1">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label class="form-label" for="is_status_method">{{ $t('setting_payment_method.lbl_is_live') }}</label>
            <div class="form-check form-switch">
              <input class="form-check-input" :true-value="1" :false-value="0" :value="midtrans_is_status"
                :checked="midtrans_is_status == 1 ? true : false" name="midtrans_is_status" id="is_status_method"
                type="checkbox" v-model="midtrans_is_status" />
            </div>
          </div>
        </div>
        <div class="col-md-10">
          <InputField class="col-md-12" type="text" :is-required="true" :label="$t('setting_payment_method.lbl_client_id')" placeholder="" v-model="midtrans_clientid" :error-message="errors['midtrans_clientid']" :error-messages="errorMessages['midtrans_clientid']"></InputField>
        </div>
      </div>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { useField, useForm } from 'vee-validate'
import { STORE_URL, GET_URL } from '@/vue/constants/setting'
import InputField from '@/vue/components/form-elements/InputField.vue'
import * as yup from 'yup';
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { onMounted } from 'vue'
import { createRequest } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const { storeRequest } = useRequest()
const IS_SUBMITED = ref(false)
//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      razor_payment_method: data.razor_payment_method || 0,
      razorpay_secretkey: data.razorpay_secretkey || '',
      razorpay_publickey: data.razorpay_publickey || '',
      str_payment_method: data.str_payment_method || 0,
      stripe_secretkey: data.stripe_secretkey || '',
      stripe_publickey: data.stripe_publickey || '',
      paystack_payment_method: data.paystack_payment_method || 0,
      paystack_secretkey: data.paystack_secretkey || '',
      paystack_publickey: data.paystack_publickey || '',
      paypal_payment_method: data.paypal_payment_method || 0,
      paypal_secretkey: data.paypal_secretkey || '',
      paypal_clientid: data.paypal_clientid || '',
      flutterwave_payment_method: data.flutterwave_payment_method || 0,
      flutterwave_secretkey: data.flutterwave_secretkey || '',
      flutterwave_publickey: data.flutterwave_publickey || '',
      cinet_payment_method: data.cinet_payment_method || 0,
      cinet_clientid: data.cinet_clientid || '',
      cinet_apikey: data.cinet_apikey || '',
      cinet_secretkey: data.cinet_secretkey || '',
      sadad_payment_method: data.sadad_payment_method || 0,
      sadad_clientid: data.sadad_clientid || '',
      sadad_secretkey: data.sadad_secretkey || '',
      sadad_domain: data.sadad_domain || '',
      airtelmoney_payment_method: data.airtelmoney_payment_method || 0,
      airtelmoney_is_status: data.airtelmoney_is_status || 0,
      airtelmoney_clientid: data.airtelmoney_clientid || 0,
      airtelmoney_secretkey: data.airtelmoney_secretkey || 0,
      phonepay_payment_method: data.phonepay_payment_method || 0,
      phonepay_is_status: data.phonepay_is_status || 0,
      phonepay_appid: data.phonepay_appid || 0,
      phonepay_merchentid: data.phonepay_merchentid || 0,
      phonepay_saltid: data.phonepay_saltid || 0,
      phonepay_saltkey: data.phonepay_saltkey || 0,
      midtrans_payment_method: data.midtrans_payment_method || 0,
      midtrans_is_status: data.midtrans_is_status || 0,
      midtrans_clientid: data.midtrans_clientid || 0,
    }
  })
}
const validationSchema = yup.object({
  razorpay_secretkey: yup.string().test('razorpay_secretkey', 'Must be a valid RazorPay key', function (value) {
    if (this.parent.razor_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  razorpay_publickey: yup.string().test('razorpay_publickey', 'Must be a valid RazorPay Publickey', function (value) {
    if (this.parent.razor_payment_method == 1 && !value) {

      return false;
    }
    return true
  }),
  stripe_secretkey: yup.string().test('stripe_secretkey', 'Must be a valid Stripe key', function (value) {
    if (this.parent.str_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  stripe_publickey: yup.string().test('stripe_publickey', 'Must be a valid Stripe Publickey', function (value) {
    if (this.parent.str_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  paystack_secretkey: yup.string().test('paystack_secretkey', 'Must be a valid Paystack key', function (value) {
    if (this.parent.paystack_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  paystack_publickey: yup.string().test('paystack_publickey', 'Must be a valid Paystack Publickey', function (value) {
    if (this.parent.paystack_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  paypal_secretkey: yup.string().test('paypal_secretkey', 'Must be a valid Paypal key', function (value) {
    if (this.parent.paypal_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  paypal_clientid: yup.string().test('paypal_clientid', 'Must be a valid Paypal Publickey', function (value) {
    if (this.parent.paypal_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  flutterwave_secretkey: yup.string().test('flutterwave_secretkey', 'Must be a valid Flutterwave key', function (value) {
    if (this.parent.flutterwave_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  flutterwave_publickey: yup.string().test('flutterwave_publickey', 'Must be a valid Flutterwave Publickey', function (value) {
    if (this.parent.flutterwave_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  cinet_clientid: yup.string().test('cinet_clientid', 'Must be a valid Cinet Clientid', function (value) {
    if (this.parent.cinet_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  cinet_apikey: yup.string().test('cinet_apikey', 'Must be a valid Cinet Apikey', function (value) {
    if (this.parent.cinet_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
  cinet_secretkey: yup.string().test('cinet_secretkey', 'Must be a valid Cinet Secretkey', function (value) {
    if (this.parent.cinet_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  sadad_clientid: yup.string().test('sadad_clientid', 'Must be a valid Sadad Clientid', function (value) {
    if (this.parent.sadad_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  sadad_secretkey: yup.string().test('sadad_secretkey', 'Must be a valid Sadad Secretkey', function (value) {
    if (this.parent.sadad_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  sadad_domain: yup.string().test('sadad_domain', 'Must be a valid Sadad Domain', function (value) {
    if (this.parent.sadad_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  airtelmoney_clientid: yup.string().test('airtelmoney_clientid', 'Must be a valid airtelmoney Clientid', function (value) {
    if (this.parent.airtelmoney_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  airtelmoney_secretkey: yup.string().test('airtelmoney_secretkey', 'Must be a valid airtelmoney key', function (value) {
    if (this.parent.airtelmoney_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  phonepay_appid: yup.string().test('phonepay_appid', 'Must be a valid Phonepay Appid', function (value) {
    if (this.parent.phonepay_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  phonepay_merchentid: yup.string().test('phonepay_merchentid', 'Must be a valid Phonepay Merchantid', function (value) {
    if (this.parent.phonepay_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  phonepay_saltid: yup.string().test('phonepay_saltid', 'Must be a valid Phonepay Saltid', function (value) {
    if (this.parent.phonepay_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  phonepay_saltkey: yup.string().test('phonepay_saltkey', 'Must be a valid Phonepay Saltkey', function (value) {
    if (this.parent.phonepay_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),

  midtrans_clientid: yup.string().test('midtrans_clientid', 'Must be a valid Midtrans Clientid', function (value) {
    if (this.parent.midtrans_payment_method == 1 && !value) {
      return false;
    }
    return true
  }),
})
const { handleSubmit, errors, resetForm } = useForm({ validationSchema })
const errorMessages = ref({})
const { value: razor_payment_method } = useField('razor_payment_method')
const { value: razorpay_secretkey } = useField('razorpay_secretkey')
const { value: razorpay_publickey } = useField('razorpay_publickey')
const { value: str_payment_method } = useField('str_payment_method')
const { value: stripe_secretkey } = useField('stripe_secretkey')
const { value: stripe_publickey } = useField('stripe_publickey')
const { value: paystack_payment_method } = useField('paystack_payment_method')
const { value: paystack_secretkey } = useField('paystack_secretkey')
const { value: paystack_publickey } = useField('paystack_publickey')
const { value: paypal_payment_method } = useField('paypal_payment_method')
const { value: paypal_secretkey } = useField('paypal_secretkey')
const { value: paypal_clientid } = useField('paypal_clientid')
const { value: flutterwave_payment_method } = useField('flutterwave_payment_method')
const { value: flutterwave_secretkey } = useField('flutterwave_secretkey')
const { value: flutterwave_publickey } = useField('flutterwave_publickey')
const { value: cinet_payment_method } = useField('cinet_payment_method')
const { value: cinet_clientid } = useField('cinet_clientid')
const { value: cinet_apikey } = useField('cinet_apikey')
const { value: cinet_secretkey } = useField('cinet_secretkey')
const { value: sadad_payment_method } = useField('sadad_payment_method')
const { value: sadad_clientid } = useField('sadad_clientid')
const { value: sadad_secretkey } = useField('sadad_secretkey')
const { value: sadad_domain } = useField('sadad_domain')
const { value: airtelmoney_payment_method } = useField('airtelmoney_payment_method')
const { value: airtelmoney_is_status } = useField('airtelmoney_is_status')
const { value: airtelmoney_clientid } = useField('airtelmoney_clientid')
const { value: airtelmoney_secretkey } = useField('airtelmoney_secretkey')
const { value: phonepay_payment_method } = useField('phonepay_payment_method')
const { value: phonepay_is_status } = useField('phonepay_is_status')
const { value: phonepay_appid } = useField('phonepay_appid')
const { value: phonepay_merchentid } = useField('phonepay_merchentid')
const { value: phonepay_saltid } = useField('phonepay_saltid')
const { value: phonepay_saltkey } = useField('phonepay_saltkey')
const { value: midtrans_payment_method } = useField('midtrans_payment_method')
const { value: midtrans_is_status } = useField('midtrans_is_status')
const { value: midtrans_clientid } = useField('midtrans_clientid')

watch(() => razor_payment_method.value, (value) => {
  if (value == '0') {
    razorpay_secretkey.value = ''
    razorpay_publickey.value = ''
  }
}, { deep: true })
watch(() => str_payment_method.value, (value) => {
  if (value == '0') {
    stripe_secretkey.value = ''
    stripe_publickey.value = ''
  }
}, { deep: true })
watch(() => paystack_payment_method.value, (value) => {
  if (value == '0') {
    paystack_secretkey.value = ''
    paystack_publickey.value = ''
  }
}, { deep: true })
watch(() => paypal_payment_method.value, (value) => {
  if (value == '0') {
    paypal_secretkey.value = ''
    paypal_clientid.value = ''
  }
}, { deep: true })
watch(() => flutterwave_payment_method.value, (value) => {
  if (value == '0') {
    flutterwave_secretkey.value = ''
    flutterwave_publickey.value = ''
  }
}, { deep: true })

watch(() => cinet_payment_method.value, (value) => {
  if (value == '0') {
    cinet_clientid.value = ''
    cinet_apikey.value = ''
    cinet_secretkey.value = ''
  }
}, { deep: true })

watch(() => sadad_payment_method.value, (value) => {
  if (value == '0') {
    sadad_clientid.value = ''
    sadad_secretkey.value = ''
    sadad_domain.value = ''
  }
}, { deep: true })

watch(() => airtelmoney_payment_method.value, (value) => {
  if (value == '0') {
    airtelmoney_clientid.value = ''
    airtelmoney_secretkey.value = ''
  }
}, { deep: true })

watch(() => phonepay_payment_method.value, (value) => {
  if (value == '0') {
    phonepay_appid.value = ''
    phonepay_merchentid.value = ''
    phonepay_saltid.value = ''
    phonepay_saltkey.value = ''
  }
}, { deep: true })

watch(() => midtrans_payment_method.value, (value) => {
  if (value == '0') {
    midtrans_clientid.value = ''
  }
}, { deep: true })
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
const data = 'razor_payment_method,razorpay_secretkey,razorpay_publickey,str_payment_method,stripe_secretkey,stripe_publickey,paystack_payment_method,paystack_secretkey,paystack_publickey,paypal_payment_method,paypal_secretkey,paypal_clientid,flutterwave_payment_method,flutterwave_secretkey,flutterwave_publickey,cinet_payment_method,cinet_clientid,cinet_apikey,cinet_secretkey,sadad_payment_method,sadad_clientid,sadad_secretkey,sadad_domain,airtelmoney_payment_method,airtelmoney_is_status,airtelmoney_clientid,airtelmoney_secretkey,phonepay_payment_method,phonepay_is_status,phonepay_appid,phonepay_merchentid,phonepay_saltid,phonepay_saltkey,midtrans_payment_method,midtrans_is_status,midtrans_clientid'
onMounted(() => {
  createRequest(GET_URL(data)).then((response) => {
    setFormData(response)
  })
})

//Form Submit
const formSubmit = handleSubmit((values) => {
  console.log(values)
  IS_SUBMITED.value = true
  const newValues = {}
  Object.keys(values).forEach((key) => {
    if (values[key] !== '') {
      newValues[key] = values[key] || 0
    }
  })
  storeRequest({
    url: STORE_URL,
    body: newValues
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
