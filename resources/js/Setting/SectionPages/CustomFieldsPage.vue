<template>
  <CardTitle :title="$t('setting_sidebar.lbl_custom_field')" icon="fa-solid fa-bars">
    <button class="btn btn-primary me-2" v-if="hasPermissions('add_custom_field')" data-bs-toggle="modal" data-bs-target="#exampleModal" aria-controls="exampleModal" @click="changeId(0)"><i class="fas fa-plus-circle me-2"></i>{{ $t('messages.new') }}</button>
  </CardTitle>

  <FormCustomField :id="tableId" @onSubmit="fetchTableData()"></FormCustomField>

  <div class="table-responsive">
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>{{ $t('custom_feild.lbl_sr_no') }}</th>
          <th>{{ $t('custom_feild.lbl_modules') }}</th>
          <th>{{ $t('custom_feild.lbl_field_lable') }}</th>
          <th>{{ $t('custom_feild.lbl_type') }}</th>
          <th>{{ $t('custom_feild.lbl_is_value') }}</th>
          <th>{{ $t('custom_feild.lbl_is_required') }}</th>
          <th>{{ $t('custom_feild.lbl_allow_table_view') }}</th>
          <th>{{ $t('custom_feild.lbl_show_table_view') }}</th>
          <th>{{ $t('custom_feild.lbl_action') }}</th>
        </tr>
      </thead>
      <template v-if="tableList !== null && tableList.length !== 0">
        <tbody>
          <tr v-for="(item, index) in tableList" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ item.custom_fields_group[0].name }}</td>
            <td>{{ item.label }}</td>
            <td>{{ item.type }}</td>
            <td>{{ item.values || '-' }}</td> <!-- Display dash if values is empty or null -->
            <td><span v-if="item.required == 1">Yes</span><span v-else>No</span></td>
            <td><span v-if="item.is_export == 1">Yes</span><span v-else>No</span></td>
            <td><span v-if="item.is_view == 1">Yes</span><span v-else>No</span></td>

            <th>
              <button type="button" v-if="hasPermissions('edit_custom_field')" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal"  :title="$t('messages.edit')"  data-bs-target="#exampleModal" @click="changeId(item.id)" aria-controls="exampleModal"><i class="fa-solid fa-pen-clip"></i></button>
              <button type="button" v-if="hasPermissions('delete_custom_field')" class="btn btn-soft-danger btn-sm ms-2" :title="$t('messages.delete')" @click="destroyData(item.id, 'Are you sure you want to delete it?')" data-bs-toggle="tooltip"><i class="fa-solid fa-trash"></i></button>
            </th>
          </tr>
        </tbody>
      </template>

      <template v-else>
        <!-- Render message when tableList is null or empty -->
        <tr class="text-center">
          <td colspan="9" class="py-3">{{ $t('messages.data_not_found') }}</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { LISTING_URL, DELETE_URL } from '@/vue/constants/custom_field'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import FormCustomField from './Forms/FormCustomField.vue'
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

const hasPermissions = (name) => {
  return window.auth_permissions.includes(name)
}

// Define variables
const tableList = ref(null)

// Fetch all data
const fetchTableData = () => {
  getRequest({ url: LISTING_URL }).then((res) => {
    if (res.status) {
      tableList.value = res.data
      tableId.value = 0
    }
  })
}

// Destroy data
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
