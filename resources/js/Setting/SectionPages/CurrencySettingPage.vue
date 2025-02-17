<template>
  <CardTitle :title="$t('setting_sidebar.lbl_currency_setting')" icon="fa fa-dollar fa-lg mr-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="exampleModal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i> {{$t('messages.new')}}</button>
  </CardTitle>
  <CurrencyFormOffCanvas :id="tableId" @onSubmit="fetchTableData()"></CurrencyFormOffCanvas>
  <div class="table-responsive">
    <table class="table table-condense">
      <thead>
        <tr>
          <th>{{ $t('currency.lbl_ID') }}</th>
          <th>{{ $t('currency.lbl_currency_name') }}</th>
          <th>{{ $t('currency.lbl_currency_symbol') }}</th>
          <th>{{ $t('currency.lbl_currency_code') }}</th>
          <th>{{ $t('currency.lbl_is_primary') }}</th>
          <th>{{ $t('currency.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="(currency, index) in tableList" :key="index">
            <th>{{ index + 1 }}</th>
            <th>{{ currency.currency_name }}</th>
            <th>{{ currency.currency_symbol }}</th>
            <th>{{ currency.currency_code }}</th>
            <th>
              <span v-if="currency.is_primary" class="badge bg-success">Default</span>
              <span v-else class="badge bg-danger">-</span>
            </th>
            <th>
              <button type="button" class="btn btn-soft-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" :title="$t('messages.edit')" @click="changeId(currency.id)" aria-controls="exampleModal"><i class="fa-solid fa-pen-clip"></i></button>
              <button type="button" class="btn btn-soft-danger btn-sm" :title="$t('messages.delete')" @click="destroyData(currency.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="fa-solid fa-trash"></i></button>
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
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import CurrencyFormOffCanvas from './Forms/CurrencyFormOffCanvas.vue'
import { LISTING_URL, DELETE_URL } from '@/vue/constants/currency'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { confirmSwal } from '@/helpers/utilities'
import SubmitButton from './Forms/SubmitButton.vue'
const tableId = ref(null)
const changeId = (id) => {
  tableId.value = id
}

// Request
const { getRequest, deleteRequest } = useRequest()

onMounted(() => {
  fetchTableData()
})

// Define variables
const tableList = ref(null)

// fetch all data
const fetchTableData = () => {
  getRequest({ url: LISTING_URL }).then((res) => {
    if (res.status) {
      tableList.value = res.data
      tableId.value = 0
    }
  })
}

// destroy data
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
