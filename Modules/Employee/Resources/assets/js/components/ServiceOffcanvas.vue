<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="package-service-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-body">
        <div class="d-flex flex-column">
          <h4>Services</h4>
          <table v-if="services.length > 0" class="table table-striped border table-responsive dataTable no-footer">
            <thead>
              <tr>
                <th>Name</th>
                <th>Price</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="serviceItem in services" :key="serviceItem.id">
                <td>
                  <h6>{{ serviceItem.service_name }}</h6>
                </td>
                <td>
                  <h6 class="text-danger">{{ formatCurrencyVue(serviceItem.service_price) }}</h6>
                </td>
              </tr>
            </tbody>
          </table>
          <p v-else class="text-muted">No data available</p>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { SERVICE_URL } from '../constant/employee'
import { useField, useForm } from 'vee-validate'
import InputField from '@/vue/components/form-elements/InputField.vue'
import { useSelect } from '@/helpers/hooks/useSelect'

import { useModuleId, useRequest, useOnOffcanvasHide, useOnOffcanvasShow } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { readFile } from '@/helpers/utilities'
import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'

// Request
const { listingRequest, getRequest, updateRequest } = useRequest()

const formatCurrencyVue = (value) => {
  if (window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}
const services = ref([])
const branchId = useModuleId(() => {
  getRequest({ url: SERVICE_URL, id: branchId.value }).then((res) => {
    if (res.status) {
      console.log(branchId.value)
      services.value = res.data
      console.log(packages.value)
    } else {
      services.value = res.data
    }
  })
}, 'package_service_form')
</script>
