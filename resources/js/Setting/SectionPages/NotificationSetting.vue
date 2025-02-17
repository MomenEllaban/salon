<template>
  <form @submit="formSubmit">
    <div>
      <CardTitle :title="$t('setting_sidebar.lbl_notification')" icon="fa-solid fa-bullhorn"></CardTitle>
    </div>
    <div>
      <table class="table table-condensed table-responsive">
        <thead>
          <tr>
            <th>{{ $t('notification.lbl_type') }}  </th>
            <th>{{ $t('notification.template') }} </th>
            <th v-for="(channel, index) in channels" :key="index">{{ channel }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(template, index) in notificationTemplates" :key="index">
            <td>{{ template.type }}</td>
            <td>{{ template.template }}</td>
            <td v-for="(channel, index) in template.channels" :key="index">
              <input type="checkbox" class="form-check-input" :true-value="1" :false-value="0" v-model="template.channels[index]" :id="`${template.type}_${channel}`" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <SubmitButton :IS_SUBMITED="IS_SUBMITED"></SubmitButton>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import CardTitle from '@/Setting/Components/CardTitle.vue'
import { GET_NOTIFICATION_URL, CHANNEL_UPDATE_URL } from '@/vue/constants/setting'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { useForm } from 'vee-validate'
import SubmitButton from './Forms/SubmitButton.vue'

// Request
const { getRequest, storeRequest } = useRequest()

// Define variables
const notificationTemplates = ref(null)
const channels = ref(null)
const IS_SUBMITED = ref(false)

// message
const display_submit_message = (res) => {
  IS_SUBMITED.value = false
  if (res.status) {
    window.successSnackbar(res.message)
  } else {
    window.errorSnackbar(res.message)
  }
}

// Make API request to get notification templates
getRequest({ url: GET_NOTIFICATION_URL }).then((res) => {
  if (res.status) {
    notificationTemplates.value = res.data
    channels.value = res.channels
  }
})

const { handleSubmit } = useForm({})

//Form Submit
const formSubmit = handleSubmit(() => {
  IS_SUBMITED.value = true
  storeRequest({ url: CHANNEL_UPDATE_URL, body: notificationTemplates.value }).then((res) => display_submit_message(res))
})
</script>
