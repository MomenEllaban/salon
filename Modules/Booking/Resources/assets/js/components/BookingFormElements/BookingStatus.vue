<template>
  <div class="d-flex align-items-center justify-content-between w-100 border-top p-3">
    <div class="d-flex align-items-center gap-3">
      <span class="p-2 border border-light rounded-circle"
        :style="{ 'background-color': filterStatus(status)?.color_hex }">
        <span class="visually-hidden">Status</span>
      </span>
      <h5 class="mb-0">{{ filterStatus(status)?.title }}</h5>
    </div>
    <template v-for="(singleStatus, key) in props.statusList" :key="singleStatus">
      <button type="button"
        v-if="props.booking_id > 0 && employee_id > 0 && filterStatus(singleStatus?.next_status)?.title && status == key"
        class="btn btn-outline-primary rounded-pill" @click="changeBookingStatus(singleStatus?.next_status)">
        {{ filterStatus(singleStatus?.next_status).title === 'Confirmed' ? 'Confirm' :
          filterStatus(singleStatus?.next_status).title }}
      </button>

    </template>
  </div>
</template>
<script setup>
import { UPDATE_STATUS } from '../../constant/booking'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
const props = defineProps({
  status: String,
  statusList: { type: Object, default: () => [] },
  employee_id: Number | String,
  booking_id: Number | String
})
const emit = defineEmits(['statusUpdate'])

const { updateRequest } = useRequest()

const filterStatus = (value) => {
  return props.statusList[value]
}
// Update Status
const changeBookingStatus = (status) => {
  const data = {
    status: status
  }
  updateRequest({ url: UPDATE_STATUS, id: props.booking_id, body: data }).then((res) => {
    if(res.status) {
      emit('statusUpdate', res.data)
      window.successSnackbar(res.message)
    }
  })
}
</script>
