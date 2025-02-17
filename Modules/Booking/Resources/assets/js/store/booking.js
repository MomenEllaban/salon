import { ref, reactive, computed } from 'vue'
import {defineStore} from 'pinia'
export const useBookingStore = defineStore('booking', () => {

  // Steps Functionality
  const step = ref('MAIN')
  const stepsList = reactive(['MAIN', 'CHECK_OUT' ,'PAYMENT'])
  const singleStep = computed(() => step.value)
  const updateStep = (value) => {
    step.value = value
  }
  const stepOptions = { step, singleStep, stepsList, updateStep }

  return {
    ...stepOptions,
  }
})
