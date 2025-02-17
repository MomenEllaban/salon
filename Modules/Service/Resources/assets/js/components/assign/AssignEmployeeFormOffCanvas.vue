<template>
  <form @submit.prevent="formSubmit">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="service-employee-assign-form" aria-labelledby="form-offcanvasLabel">
      <div class="offcanvas-header border-bottom" v-if="service">
        <h6 class="m-0 h5">
          {{ $t('service.singular_title') }}: <span>{{ service.name }}</span>
        </h6>
      </div>
      <div class="offcanvas-body">
        <div class="form-group">
          <div class="d-grid">
            <div class="d-flex flex-column">
              <div class="mb-4">
                <!-- <Multiselect id="branch_id" placeholder="Select Branch" v-model="branch_id" :value="branch_id" v-bind="singleSelectOption" :options="branch.options" @select="branchSelect" class="form-group mb-3"></Multiselect> -->
                <Multiselect v-model="assign_ids" placeholder="Select Staff"  :value="assign_ids" :canClear="false" v-bind="singleSelectOption" :options="employee.options" @select="selectEmployee" id="employees_ids">
                  <template v-slot:multiplelabel="{ values }">
                    <div class="multiselect-multiple-label">{{ $t('messages.select_staff')}}</div>
                  </template>
                </Multiselect>
              </div>
            </div>
            <div class="list-group list-group-flush">
              <div v-for="(item, index) in selectedEmployee" :key="item" class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex justify-between flex-grow-1 gap-2 my-2">
                  <img :src="item.avatar" class="avatar avatar-40 img-fluid rounded-pill" alt="user" />
                  <div class="flex-grow-1 mt-2"> {{ item.name }}</div>
                </div>
                <button type="button" @click="removeEmployee(item.employee_id)" class="btn btn-sm text-danger"><i class="fa-regular fa-trash-can"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="offcanvas-footer">
        <p class="text-center mb-0"><small>{{ $t('service.assign_staff_to_service')}}</small></p>
        <div class="d-grid gap-3 p-3">
          <button class="btn btn-primary d-block">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $t('messages.update')}}
          </button>
          <button class="btn btn-outline-primary d-block" type="button" data-bs-dismiss="offcanvas">
            <i class="fa-solid fa-angles-left"></i>
            {{ $t('messages.close')}}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { POST_EMPLOYEE_ASSIGN_URL, GET_EMPLOYEE_ASSIGN_URL, EDIT_URL, EMPLOYEE_LIST, BRANCH_LIST } from '../../constant/service'
import { useModuleId, useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'
import { useSelect } from '@/helpers/hooks/useSelect'

// Request
const { listingRequest, getRequest, updateRequest } = useRequest()

// Vue Form Select START
const branch = ref({ options: [], list: [] })
const employee = ref({ options: [], list: [] })
const selectedEmployee = ref([])
// Vue Form Select END
// Select Options
const singleSelectOption = ref({
  mode: 'multiple',
  closeOnSelect: false,
  searchable: true
})
// Form Values
const assign_ids = ref([])
const branch_id = ref(null)
const service = ref(null)
const serviceId = useModuleId(() => {
  getRequest({ url: GET_EMPLOYEE_ASSIGN_URL, id: serviceId.value }).then((res) => {
    if (res.status && res.data) {
      selectedEmployee.value = res.data
      assign_ids.value = res.data.map((item) => item.employee_id)
    }
  })
  getRequest({ url: EDIT_URL, id: serviceId.value }).then((res) => res.status && (service.value = res.data))
}, 'employee_assign')

onMounted(() => {
  useSelect({ url: BRANCH_LIST }, { value: 'id', label: 'name' }).then((data) => {
    branch.value = data
    if (data.options.length === 1) {
      branch_id.value = data.options[0].value
    }
  })
  branchSelect(branch_id.value ?? '')
})

// On Select
const branchSelect = (value) => {
  useSelect({ url: EMPLOYEE_LIST, data: { branch_id: value } }, { value: 'id', label: 'name' }).then((data) => (employee.value = data))
}

// Reload Datatable, SnackBar Message, Alert, Offcanvas Close
const errorMessages = ref([])
const reset_close_offcanvas = (res) => {
  if (res.status) {
    window.successSnackbar(res.message)
    bootstrap.Offcanvas.getInstance('#service-employee-assign-form').hide()
    renderedDataTable.ajax.reload(null, false)
  } else {
    window.errorSnackbar(res.message)
    errorMessages.value = res.all_message
  }
}

const formSubmit = () => {
  const data = { employees: [] }
  for (let index = 0; index < selectedEmployee.value.length; index++) {
    const element = selectedEmployee.value[index]
    data.employees.push({
      employee_id: element.employee_id,
      service_id: element.service_id // Push service_id here
    })
  }
  updateRequest({ url: POST_EMPLOYEE_ASSIGN_URL, id: serviceId.value, body: data }).then((res) => reset_close_offcanvas(res))
}

const selectEmployee = (value) => {
  const currentEmployee = employee.value.list.find((emp) => emp.id === value)
  const newEmployee = {
    service_id: serviceId,
    employee_id: value,
    name: currentEmployee.name,
    avatar: currentEmployee.avatar
  }
  selectedEmployee.value = [...selectedEmployee.value, newEmployee]
}

const removeEmployee = (value) => {
  selectedEmployee.value = [...selectedEmployee.value.filter((emp) => emp.employee_id !== value)]
  assign_ids.value = [...assign_ids.value.filter((emp) => emp !== value)]
}
</script>
