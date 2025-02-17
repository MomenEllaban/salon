<template>
  <form @submit="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
      <FormHeader :currentId="currentId" :editTitle="editTitle" :createTitle="createTitle"></FormHeader>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-12">
            <InputField class="col-md-6" :is-required="true" :label="$t('plan.lbl_name')"  placeholder="" v-model="name" :error-message="errors.name" :error-messages="errorMessages['name']"></InputField>
            <div class="form-group">
              <label class="form-label" for="type">{{ $t('plan.lbl_type') }} <span class="text-danger">*</span> </label>
              <Multiselect v-model="type" :value="type" v-bind="type_data" id="type" autocomplete="off"></Multiselect>
              <span v-if="errorMessages['type']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['type']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.type }}</span>
            </div>

            <div class="form-group" v-if="type === 'Monthly'">
              <label class="form-label" for="duration">{{ $t('plan.lbl_duration') }}</label>
              <Multiselect v-model="duration" :value="duration" v-bind="duration_data" id="duration" autocomplete="off"></Multiselect>
              <span v-if="errorMessages['duration']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['duration']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.duration }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="amount">{{ $t('plan.lbl_amount') }}<span class="text-danger">*</span> </label>
              <input type="number" class="form-control" v-model="amount" id="amount" autocomplete="off" />
              <span v-if="errorMessages['amount']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['amount']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.amount }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="planlimitation">{{ $t('plan.lbl_plan_limitation') }}<span class="text-danger">*</span> </label>
              <Multiselect v-model="planlimitation" :value="planlimitation" v-bind="planlimitation_data" @select="selectplanLimitation" id="planlimitation"></Multiselect>
              <span v-if="errorMessages['planlimitation']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['planlimitation']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.planlimitation }}</span>
            </div>

            <div class="form-group">
              <label class="form-label" for="description">{{ $t('plan.lbl_description') }}</label>
              <textarea class="form-control" v-model="description" id="description"></textarea>
              <span v-if="errorMessages['description']">
                <ul class="text-danger">
                  <li v-for="err in errorMessages['description']" :key="err">{{ err }}</li>
                </ul>
              </span>
              <span class="text-danger">{{ errors.description }}</span>
            </div>

            <div class="form-group">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label" for="category-status">{{ $t('plan.lbl_status') }}</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" :value="1" name="status" id="category-status" type="checkbox" v-model="status" />
                </div>
              </div>
            </div>

            <div class="form-group" v-if="planlimitation === 'Limited'" id="limitation_data">
              <label class="form-label" for="planlimitation_id">{{ $t('plan.lbl_select_limitation') }}</label>

              <a href="/app/subscription/planlimitation"
                ><h6 for="planlimitation_id" style="float: right"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create Limitation</h6>
              </a>

              <div class="mb-4">
                <Multiselect v-model="planlimitation_id" :multiple="true" :value="planlimitation_id" v-bind="LimitationData" @select="selectLimitation" @deselect="removeLimitation" id="planlimitation_id"></Multiselect>
              </div>

              <div v-for="item in selectedLimitationData" :key="item" class="align-items-center border card card-body shadow-none flex-row mb-3 p-3">
                <div class="flex-grow-1 ms-3" :id="'limitation-' + item.id">
                  <h5 class="mb-0">{{ item.name }}</h5>
                </div>
                <div class="d-flex justify-content-end align-items-center gap-3 w-50">
                  <!-- <h6 class="mb-0">Limit: {{ item.limit}}</h6>  -->
                  <label class="form-label">{{ $t('plan.lbl_set_limit') }}</label>
                  <input v-model="item.limit" type="number" class="form-control w-50" :min="0" />
                </div>
              </div>
            </div>

            <div v-for="field in customefield" :key="field.id">

                <FormElement v-model="custom_fields_data" :name="field.name" :label="field.label" :type="field.type" :required="field.required" :options="field.value"  :field_id="field.id"  ></FormElement>
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
import { EDIT_URL, STORE_URL, UPDATE_URL, LIMITATIONDATA_LIST } from '../constant/plan'
import { useField, useForm } from 'vee-validate'
import { useModuleId, useRequest,useOnOffcanvasHide } from '@/helpers/hooks/useCrudOpration'
import * as yup from 'yup'
import { buildMultiSelectObject } from '@/helpers/utilities'

import FormHeader from '@/vue/components/form-elements/FormHeader.vue'
import FormFooter from '@/vue/components/form-elements/FormFooter.vue'
import InputField from '@/vue/components/form-elements/InputField.vue'
import FormElement from '@/helpers/custom-field/FormElement.vue'
// props
const props = defineProps({
  createTitle: { type: String, default: '' },
  editTitle: { type: String, default: '' },
  customefield: { type: Array, default: () => [] }
})
const selectedLimitationData = ref([])
const planlimitation_id = ref([])

const { getRequest, storeRequest, updateRequest, listingRequest } = useRequest()

onMounted(() => {

  setFormData(defaultData())
})
const currentId = useModuleId(() => {
  if (currentId.value > 0) {
    getRequest({ url: EDIT_URL, id: currentId.value }).then((res) => {
      if (res.status && res.data) {
        setFormData(res.data)
        selectedLimitationData.value = res.data.plan_limitation
        planlimitation_id.value = res.data.plan_limitation.map((item) => item.planlimitation_id)
      }
    })
  } else {
    setFormData(defaultData())
  }
})



/*
 * Form Data & Validation & Handeling
 */
// Default FORM DATA
const defaultData = () => {
  errorMessages.value = {}
  return {
    name: '',
    type: '',
    duration: '',
    amount: '',
    planlimitation: '',
    description: '',
    status: 'true',
    custom_fields_data: {
    }
  }
}

//  Reset Form
const setFormData = (data) => {
  resetForm({
    values: {
      name: data.name,
      type: data.type,
      duration: data.duration,
      amount: data.amount,
      planlimitation: data.planlimitation,
      description: data.description,
      status: data.status ? true : false,
      custom_fields_data: data.custom_field_data
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

// Validations
const validationSchema = yup.object({
  name: yup.string()
    .required('Name is a required field')
    .test('is-string', 'Only strings are allowed', (value) => !numberRegex.test(value)),
  type: yup.string().required('Type is a required field'),
  amount: yup.string()
    .required('Amount is a required field')
    .matches(/^\d+$/, 'Only numbers are allowed')
    .min(0),
  planlimitation: yup.string().required('Plan Limitation is a required field')
})

const { handleSubmit, errors, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: type } = useField('type')
const { value: duration } = useField('duration')
const { value: amount } = useField('amount')
const { value: planlimitation } = useField('planlimitation')
const { value: description } = useField('description')
const { value: status } = useField('status')
const { value: custom_fields_data } = useField('custom_fields_data')
const errorMessages = ref({})

const planlimitation_data = ref({
  searchable: true,
  options: [
    { label: 'Unlimited', value: 'Unlimited' },
    { label: 'Limited', value: 'Limited' }
  ],
  createOption: true,
  closeOnSelect: true
})

const duration_data = ref({
  searchable: true,
  options: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
  createOption: true,
  closeOnSelect: true
})

const type_data = ref({
  searchable: true,
  options: [
    // { label: 'Weekly', value: 'Weekly'},
    { label: 'Monthly', value: 'Monthly' },
    { label: 'Yearly', value: 'Yearly' }
  ],
  createOption: true,
  closeOnSelect: true
})

const LimitationData = ref({
  searchable: true,
  options: [],
  createOption: true,
  closeOnSelect: true,
  mode: 'tags'
})

//   const getLimitationDataList = () => {
//     listingRequest({ url: LIMITATIONDATA_LIST, data: { id: 0 } }).then((res) => (LimitationData.value.options = buildMultiSelectObject(res, { value: 'id', label: 'text' })))
//   }

//  onMounted(() => {
//    getLimitationDataList()
//  })

const LimitationData1 = ref([])
onMounted(() => {
  listingRequest({ url: LIMITATIONDATA_LIST }).then((res) => {
    LimitationData1.value = res
    LimitationData.value.options = buildMultiSelectObject(res, { value: 'id', label: 'name' })
  })
})

const selectLimitation = (value) => {
  const currentLimitation = LimitationData1.value.find((limit) => limit.id === value)

  const newLimitation = {
    planlimitation_id: value,
    name: currentLimitation.name,
    limit: currentLimitation.limit
  }
  selectedLimitationData.value = [...selectedLimitationData.value, newLimitation]
}

const removeLimitation = (value) => {
  selectedLimitationData.value = [...selectedLimitationData.value.filter((limit) => limit.planlimitation_id !== value)]
}

// Form Submit
const IS_SUBMITED = ref(false)
const formSubmit = handleSubmit((values) => {
  if(IS_SUBMITED.value) return false

  const data = []
  for (let index = 0; index < selectedLimitationData.value.length; index++) {
    const element = selectedLimitationData.value[index]
    data.push({
      planlimitation_id: element.planlimitation_id,
      limit: element.limit
    })
  }

  values.data = JSON.stringify(data)
  values.custom_fields_data = JSON.stringify(values.custom_fields_data)


  if (currentId.value > 0) {
    updateRequest({ url: UPDATE_URL, id: currentId.value, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  } else {
    storeRequest({ url: STORE_URL, body: values, type: 'file' }).then((res) => reset_datatable_close_offcanvas(res))
  }
})
const selectplanLimitation = (value) => {

}

useOnOffcanvasHide('form-offcanvas', () => setFormData(defaultData()))
</script>
