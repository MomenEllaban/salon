<template>
  <div class="booking-wizard">
    <div class="container-fluid p-0">
      <div class="widget-layout">
        <div class="non-printable">
          <div class="iq-card iq-card-sm bg-primary widget-tabs">
            <ul class="tab-list">
              <template v-for="(item, index) in setupArray" :key="`items-${index}`">
                <li :class="`${activeCheck(item.id)}  tab-item`" :data-check="`${doneCheck(item.id)}`" v-if="item.is_vissible">
                  <a class="tab-link" :href="`#${item.type}`" :id="`${item.type}-tab`">
                    <h5>{{ $t(item.title) }}</h5>
                    <p v-if="item.detail">{{ $t(item.detail) }}</p>
                  </a>
                </li>
              </template>
            </ul>
          </div>
        </div>
        <div class="widget-pannel">
          <div id="wizard-tab" class="iq-card iq-card-sm tab-content">
            <template v-for="(item, index) in setupArray" :key="`panel-${index}`">
              <div :id="item.type" :class="`iq-fade iq-tab-pannel ${activeCheck(item.id)}`">
                <TabPanel :type="item.type" :title="item.title" :wizard-next="item.next" :wizard-prev="item.prev" @onClick="nextTabChange" />
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import TabPanel from './TabPanel.vue'

// Setup Array
const setupArray = reactive([

  {
    id: 1,
    title: 'quick_booking.lbl_select_branch',
    type: 'select-branch',
    is_vissible: true,
    detail: 'quick_booking.lbl_brach_description',
    done: false,
    next: 2,
    prev: null
  },
  {
    id: 2,
    title: 'quick_booking.lbl_select_service',
    type: 'select-service',
    is_vissible: true,
    detail: 'quick_booking.lbl_service_description',
    done: false,
    next: 3,
    prev: 1
  },
  {
    id: 3,
    title: 'quick_booking.lbl_select_select_staff',
    type: 'select-employee',
    is_vissible: true,
    detail: 'quick_booking.lbl_staff_description',
    done: false,
    next: 4,
    prev: 2
  },
  {
    id: 4,
    title: 'quick_booking.lbl_select_date_time',
    type: 'select-date-time',
    is_vissible: true,
    detail: 'quick_booking.lbl_sate_time_description',
    done: false,
    next: 5,
    prev: 3
  },
  {
    id: 5,
    title: 'quick_booking.lbl_customer_detail',
    type: 'customer-details',
    is_vissible: true,
    detail: 'quick_booking.lbl_customer_description',
    done: false,
    next: 6,
    prev: 4
  },
  {
    id: 6,
    title: 'quick_booking.lbl_confrimation',
    type: 'select-confirm',
    is_vissible: true,
    detail: 'quick_booking.lbl_confrim_msg',
    done: false,
    next: 7,
    prev: 5
  },
  {
    id: 7,
    title: 'quick_booking.lbl_confrimation_detail',
    type: 'confirmation-detail',
    is_vissible: false,
    detail: 'quick_booking.lbl_confrim_description',
    done: false,
    next: null,
    prev: null
  },
])
const currentindex = ref(1)
const activeCheck = (value) => (currentindex.value == value ? 'active' : '')
const doneCheck = (value) => (currentindex.value > value ? true : false)
const nextTabChange = (val) => {
  currentindex.value = val
}
</script>
