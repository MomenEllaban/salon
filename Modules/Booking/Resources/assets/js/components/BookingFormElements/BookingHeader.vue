<template>
  <div class="d-flex gap-2 align-items-center">
    <h4 class="offcanvas-title" id="form-offcanvasLabel">
      <template v-if="booking_id > 0">
        {{ $t('booking.lbl_appointment') }}
      </template>
      <template v-else>{{ $t('booking.lbl_new_appointment') }} </template>
    </h4>
    <small class="badge bg-success" v-if="is_paid">{{ $t('booking.lbl_is_paid') }}</small>
  </div>
  <div class="d-flex align-items-center gap-2">
    <div v-if="booking_id > 0 && status !== 'cancelled' && status !== 'check_in'">
      <strong
        ><button data-bs-toggle="tooltip" title="Cancel Booking" type="button" @click="changeBookingStatus('cancelled', 'Are You Sure You want to Cancel?')"  class="btn btn-soft-danger btn-sm" ><i data-v-f9741b98="" class="fa-solid fa-trash"></i></button
      ></strong>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
</template>
<script setup>
import { UPDATE_STATUS } from '../../constant/booking'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { confirmcancleSwal } from '@/helpers/utilities'
const props = defineProps({
  booking_id: { type: [Number, String], default: 0 },
  status: { type: String, default: 'pending' },
  is_paid: { type: Number, default: 0 }
})
const emit = defineEmits(['statusUpdate'])

const { updateRequest } = useRequest()

// Update Status
const changeBookingStatus = (status, message) => {
  confirmcancleSwal({ title: message }).then((result) => {
    if (!result.isConfirmed) return

    const data = {
      status: status
    }
    updateRequest({ url: UPDATE_STATUS, id: props.booking_id, body: data }).then((res) => {
      if (res.status) {
        Swal.fire({
          title: 'Appointment Cancelled !',
          //text: res.message,
          icon: 'success',
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__zoomOut'
          }
        })
      }
      res.status && emit('statusUpdate', res.data)
    })
  })
}
</script>
