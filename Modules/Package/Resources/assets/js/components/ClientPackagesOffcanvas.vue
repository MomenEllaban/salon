<template>
  <form>
  <div class="offcanvas offcanvas-end" tabindex="-1" id="client-package-form" aria-labelledby="form-offcanvasLabel">
    <FormHeader :currentId="currentId" editTitle="View package" createTitle="View Package"></FormHeader>

    <div class="offcanvas-body">
      <div class="mb-3">
        <p class="mb-0">{{$t('package.lbl_name')}}</p>
        <h6>{{ packages.packageName}}</h6>
      </div>
      <div class="mb-3">
        <p class="mb-0">{{$t('package.lbl_Client')}}</p>
        <h6>{{ packages.clientName}}</h6>
      </div>
      <div class="mb-3">
        <p class="mb-0">{{$t('package.lbl_purchase_date')}}</p>
        <h6>{{formatDate(packages.purchesDate)}}</h6>
      </div>
      <div class="mb-3">
        <p class="mb-0">{{$t('package.lbl_expiration_date')}}</p>
        <h6>{{ formatDate(packages.expirationDate)}}</h6>
      </div>
      <div>
        <h6>{{$t('package.lbl_service_remaining')}}</h6>
        <div class="table-responsive">
              <table class="table table-striped border table-responsive dataTable no-footer">
                <thead>
                  <tr>
                    <th>{{$t('package.lbl_service')}}</th>
                    <th>{{$t('package.lbl_remainig')}} </th>
                  </tr>
                </thead>
                <tr v-for="service in packages.services">
                  <td class="w-50">
                    <div><label>{{service.package_service.service_name}}</label></div>
                  </td>
                  <td>
                   <div><label>{{service.qty }}</label></div>
                  </td>
                </tr>
              </table>
            </div>
      </div>

  </div>
  </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed ,reactive} from 'vue'
import { CLIENT_PACKAGE} from '../constant'
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

const packages = reactive({
  packageName:'',
  clientName:'',
  purchesDate:'',
  expirationDate:'',
  services:[],
})
const clientpackageId = useModuleId(() => {
  getRequest({ url: CLIENT_PACKAGE, id: clientpackageId.value }).then((res) => {
      if (res.status) {
        packages.packageName=res.data.package.name;
        packages.clientName=res.data.booking.user === null ? "Unknow User" : res.data.booking.user.full_name;
        packages.purchesDate=new Date(res.data.package.start_date)
        packages.services=res.data.user_package_services;
        packages.expirationDate=new Date(res.data.package.end_date);
    }
  })

}, 'client-package-form')

const formatDate = (date) => {
  return date ? date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '';
}
</script>


