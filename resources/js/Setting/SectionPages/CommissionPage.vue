<template>
  <CardTitle :title="$t('setting_sidebar.lbl_commission')" icon="fa fa-percent fa-lg mr-2">
    <button class="btn btn-primary" v-if="hasPermissions('add_commission')" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="form-modal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i>{{ $t('messages.new') }}</button>
  </CardTitle>
  <CommissionForm :id="tableId" @onSubmit="fetchTableData()"></CommissionForm>
  <div class="table-responsive">
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>{{ $t('commission.lbl_sr_no') }}</th>
          <th>{{ $t('commission.lbl_title') }}</th>
          <th>{{ $t('commission.lbl_value') }}</th>
          <th>{{ $t('commission.lbl_type') }}</th>
          <th>{{ $t('commission.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="(item, index) in tableList" :key="index">
            <th>{{ index + 1 }}</th>
            <th>{{ item.title }}</th>
            <th>
              <span v-if="item.commission_type === 'percentage'"> {{ item.commission_value }}% </span>
              <span v-else>
                {{ formatCurrencyVue(item.commission_value) }}
              </span>
            </th>
            <th class="text-capitalize">{{ item.commission_type }}</th>
            <th>
              <button type="button" v-if="hasPermissions('edit_commission')" class="btn btn-soft-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" :title="$t('messages.edit')" @click="changeId(item.id)" aria-controls="form-offcanvas"><i class="fa-solid fa-pen-clip"></i></button>
              <button type="button" v-if="hasPermissions('delete_commission')" class="btn btn-soft-danger btn-sm" :title="$t('messages.delete')" @click="destroyData(item.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="fa-solid fa-trash"></i></button>
            </th>
          </tr>
        </tbody>
      </template>
      <template v-else>
        <!-- Render message when tableList is null or empty -->
        <tr class="text-center">
          <td colspan="9" class="py-3">Data is not available in this Table</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { LISTING_URL, DELETE_URL } from '@/vue/constants/commission'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import CommissionForm from './Forms/CommissionForm.vue'
import { confirmSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const tableId = ref(null)
const changeId = (id) => {
  tableId.value = id
}
const formatCurrencyVue = window.currencyFormat

const hasPermissions = (name) => {
  return window.auth_permissions.includes(name)
}

onMounted(() => {
  fetchTableData()
})

// Request
const { getRequest, deleteRequest } = useRequest()

// Define variables
const tableList = ref(null)

const fetchTableData = () => {
  getRequest({ url: LISTING_URL }).then((res) => {
    if (res.status) {
      tableList.value = res.data
      tableId.value = 0
    }
  })
}

watch(
  () => tableId.value,
  () => {
    const modal = document.getElementById('exampleModal')
    if (modal) {
      modal.addEventListener('hide.bs.modal', () => {
        tableId.value = 0
      })
    }
  }
)

const destroyData = (id, message) => {
  confirmSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return
    deleteRequest({ url: DELETE_URL, id }).then((res) => {
      if (res.status) {
        Swal.fire({
          title: 'Deleted',
          text: res.message,
          icon: 'success',
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__zoomOut'
          }
        })
        fetchTableData()
      }
    })
  })
}
</script>
