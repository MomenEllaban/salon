<template>
  <template v-if="bookingData">
    <div class="offcanvas-header">
      <h4>
        {{ $t('booking.sale') }} #{{ bookingData.id }} <span class="text-capitalize">({{ bookingData.status }})</span>
      </h4>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="">
        <div class="d-flex align-items-start gap-3 mb-2">
          <img :src="bookingData.user_profile_image" alt="avatar" class="img-fluid avatar avatar-60 rounded-pill" />
          <div class="flex-grow-1">
            <div class="gap-2">
              <strong>{{ bookingData.user_name }}</strong>
              <p class="m-0">
                <small>{{ $t('booking.client_since') }} {{ moment(bookingData.user_created).format('MMMM YYYY') }}</small>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div v-for="(service, index) in bookingData.services" :key="index" class="py-2">
          <div class="d-flex flex-column gap-1">
            <div class="d-flex align-items-center justify-content-between">
              <h6 class="m-0">{{ service.service_name }}</h6>
              <div class="d-flex gap-3">
                <p class="m-0">{{ formatCurrencyVue(service.service_price) }}</p>
              </div>
            </div>
            <p class="m-0">
              <label><i>{{ $t('booking.with') }}</i></label> <strong>{{ bookingData.employee_name }}</strong>
            </p>
          </div>
        </div>
      </div>

      <div v-if="bookingData.products != null" class="form-group border-top">
        <div v-for="(product, index) in bookingData.products" :key="index" class="py-2">
          <div class="d-flex flex-column gap-1">
            <div class="d-flex align-items-center justify-content-between">
              <h6 class="me-3 w-80 mb-0">
                {{ product.product_name }} (<strong>{{ product.product_qty }}</strong
                >)
              </h6>
              <div class="d-flex">
                <b v-if="product.discounted_price">{{ formatCurrencyVue(product.discounted_price) }}</b>
                <b v-else>{{ formatCurrencyVue(product.product_price) }}</b>
              </div>
            </div>
            <p class="m-0">
              <label><i>{{ $t('booking.sold_by') }}</i></label> <strong>{{ bookingData.employee_name }}</strong>
            </p>
          </div>
        </div>
      </div>

      <div v-if="bookingData.bookingPackages != null" class="form-group border-top">
        <div v-for="(packages, index) in bookingData.bookingPackages" :key="index" class="py-2">
          <div class="form-group">
            <div class="d-flex flex-column gap-1">
              <div class="d-flex align-items-center justify-content-between">
                <h6 class="m-0">{{ packages.name }}</h6>
                <div class="d-flex gap-3">
                  <p class="m-0">{{ formatCurrencyVue(packages.package_price) }}</p>
                </div>
              </div>
              <p class="m-0">
                <label><i>{{ $t('booking.with') }}</i></label> <strong>{{ bookingData.employee_name }}</strong>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="border-top border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
          <span>{{ $t('booking.payment_type') }}</span>
          <span class="badge bg-primary"
            ><strong>{{ formatTransactionType(bookingTransaction.transaction_type) }}</strong></span
          >
        </div>
        <div class="d-flex justify-content-between align-items-center my-2">
          <span>{{ $t('booking.service_amount') }}</span>
          <span
            ><strong>{{ formatCurrencyVue(servicesTotal) }}</strong></span
          >
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span>{{ $t('booking.product_amount') }}</span>
          <span
            ><strong>{{ formatCurrencyVue(productTotal) }}</strong></span
          >
        </div>
        <div class="d-flex justify-content-between align-items-center my-2">
          <span>{{ $t('booking.package_amount') }}</span>
          <span
            ><strong>{{ formatCurrencyVue(packageTotal) }}</strong></span
          >
        </div>
        <div v-if="couponDiscount > 0" class=" d-flex justify-content-between align-items-center my-2">
          <span>{{ $t('booking.coupon_discount') }}: </span>
          <span><strong class="text">-{{ formatCurrencyVue(couponDiscount) }}</strong></span>
      </div>
      <div class=" d-flex justify-content-between align-items-center my-2">
        <label for=""> Subtotal: </label>
        <strong>{{ formatCurrencyVue(subtotal ) }}</strong>
      </div>

        <div class="d-flex justify-content-between align-items-center" v-for="(tax, index) in bookingTransaction.tax_percentage" :key="index">
          <template v-if="tax.type == 'percent'">
            <span class="mb-2">{{ tax.name }}: {{ tax.percent }}% </span>
            <span
              ><strong>{{ formatCurrencyVue(calculatePercentAmount(tax.percent)) }}</strong></span
            >
          </template>
          <template v-else>
            <span class="mb-2">{{ tax.name }}: </span>
            <span
              ><strong>{{ formatCurrencyVue(tax.tax_amount) }}</strong></span
            >
          </template>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span>{{ $t('booking.tip_amount') }}</span>
          <span
            ><strong>{{ formatCurrencyVue(bookingTransaction.tip_amount) }}</strong></span
          >
        </div>
      </div>
      
      <div>
  <div v-if="couponDiscount > 0" class="form-group d-flex align-items-center justify-content-between border-top">
    <label for="">{{ $t('booking.final_total') }}: </label>
    <strong class="text">{{ formatCurrencyVue(finalAmount - couponDiscount) }}</strong>
  </div>
  <div v-else class="form-group d-flex align-items-center justify-content-between">
    <label for="">{{ $t('booking.total') }}: </label>
    <strong>{{ formatCurrencyVue(finalAmount) }}</strong>
  </div>
</div>

      <div class="py-3">
        <p><strong>{{ $t('booking.booking_detail') }}</strong></p>
        <div>
          <h6><i class="fa-regular fa-clock"></i> {{ $t('booking.booked_on') }} {{ bookingData.created_at }}</h6>
        </div>
        <div class="">
          <h6><i class="fa-regular fa-user"></i> {{ $t('booking.booked_by') }} {{ bookingData.created_by_name }}</h6>
        </div>
        <div class="mt-4">
          <h6><i class="fa-regular fa-clock"></i> {{ $t('booking.updated_on') }} {{ bookingData.updated_at }}</h6>
        </div>
        <div class="">
          <h6><i class="fa-regular fa-user"></i> {{ $t('booking.updated_by') }} {{ bookingData.updated_by_name }}</h6>
        </div>
      </div>
    </div>
  </template>
  <template v-else>
    <div class="offcanvas-header">
      <div>
        <h4>{{ $t('booking.sale') }} <span class="text-capitalize">(___________)</span></h4>
      </div>
    </div>
  </template>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import { BOOKING_DETAIL } from '../../constant/booking'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import * as moment from 'moment'

const { getRequest } = useRequest()
const props = defineProps({
  booking_id: { required: true }
})
const formatCurrencyVue = window.currencyFormat
const bookingData = ref(null)
const servicesTotal = ref(0)

const productTotal = computed(() => {
  let total = 0
  if (bookingData.value && bookingData.value.products) {
    total = bookingData.value.products.reduce((accumulator, product) => {
      const totalPriceForProduct = product.discounted_price ? product.discounted_price : product.product_price
      return accumulator + totalPriceForProduct * product.product_qty
    }, 0)
  }
  return total
})
// const productTotal = ref(0)
const couponDiscount = ref(0)
const packageTotal = ref(0)
const bookingTransaction = ref(null)
const calculatePercentAmount = (percent) => {
  const percentAmount = ((servicesTotal.value  + productTotal.value + packageTotal.value - couponDiscount.value) * percent) / 100
  return percentAmount
}
const subtotal = computed(() => {
  return servicesTotal.value  + productTotal.value + packageTotal.value - couponDiscount.value;
})

const taxAmount = computed(() => {
  let totalTaxAmount = 0
  if (bookingTransaction.value !== null) {
    for (const tax of bookingTransaction.value.tax_percentage) {
      if (tax.type === 'percent') {
        totalTaxAmount += ((servicesTotal.value  + productTotal.value + packageTotal.value - couponDiscount.value) * tax.percent) / 100
      } else {
        totalTaxAmount += tax.tax_amount
      }
    }
    return totalTaxAmount.toFixed(2)
  }
  return totalTaxAmount.toFixed(2)
})
const finalAmount = computed(() => {
  return Number(servicesTotal.value) + Number(productTotal.value) + Number(taxAmount.value) + Number(packageTotal.value) + Number(bookingTransaction.value.tip_amount)
})
onMounted(() => {
  getRequest({ url: BOOKING_DETAIL, id: props.booking_id }).then((res) => {
    if (res.status) {
      bookingData.value = res.data.booking
      servicesTotal.value = res.data.services_total_amount
      productTotal.value = res.data.product_amount
      packageTotal.value = res.data.package_amount
      bookingTransaction.value = res.data.booking_transaction
      couponDiscount.value = res.data.coupon_discount
    }
  })
})

const capitalizeFirstLetter = (string) => {
  return string.charAt(0).toUpperCase() + string.slice(1)
}
const formatTransactionType = (type) => {
  // Check if the transaction type is "Upi" and capitalize it as "UPI"
  if (type.toLowerCase() === 'upi') {
    return 'UPI'
  }
  // For other types, capitalize only the first letter
  return capitalizeFirstLetter(type)
}
</script>
