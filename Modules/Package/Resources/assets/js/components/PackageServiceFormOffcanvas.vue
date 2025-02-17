<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="package-service-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header">
        <h2 class="mb-0">{{ packages.name }}</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
        <div class="offcanvas-body">
          <div class="d-flex flex-column">
        <!-- <p>Description: {{ packages.description }}</p> -->
            <h4>Services</h4>
          <!-- Displaying services for the current package -->
          <table class="table table-striped border table-responsive dataTable no-footer">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Discounted Price</th>
                  </tr>
                </thead>
                <tr v-for="serviceItem in packages.service">
                  <td>
                    <h6>{{ serviceItem.service_name }}</h6>
                  </td>
                  <td>
                   <h6 class="text-danger">{{ formatCurrencyVue(serviceItem.service_price) }}</h6>
                  </td>
                  <td>
                   <h6 class="text-success">{{ formatCurrencyVue(serviceItem.discounted_price) }}</h6>
                  </td>
                </tr>
              </table>
          <!-- <div v-for="serviceItem in packages.service" :key="serviceItem.id">
            <div class="d-flex align-items-center gap-2 mb-1"><p class="mb-0">Name:</p><h6 class="mb-0">{{ serviceItem.service_name }}</h6></div>
            <p class="mb-1">Price: <span class="text-danger">{{ formatCurrencyVue(serviceItem.service_price) }}</span></p>
            <p class="mb-3">Discounted price: <span class="text-success">{{ formatCurrencyVue(serviceItem.discounted_price) }}</span></p>
          </div> -->
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { EDIT_URL} from '../constant'
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
const {listingRequest, getRequest, updateRequest} = useRequest()


const formatCurrencyVue = (value) => {
  if(window.currencyFormat !== undefined) {
    return window.currencyFormat(value)
  }
  return value
}
const packages = ref([])
const branchId = useModuleId(() => {
  getRequest({ url: EDIT_URL, id: branchId.value }).then((res) => {
      if (res.status) {
        console.log(branchId.value);
      packages.value = res.data
      console.log(packages.value)
      }
    })
}, 'package_service_form')

</script>

