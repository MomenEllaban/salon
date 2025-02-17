import { InitApp } from '@/helpers/main'
import EmployeeOffcanvas from './components/EmployeeOffcanvas.vue'
import ChangePassword from './components/ChangePassword.vue'

import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

import ServiceOffcanvas from './components/ServiceOffcanvas.vue'
const app = InitApp()

app.use(VueTelInput)
app.component('employee-offcanvas', EmployeeOffcanvas)
app.component('change-password', ChangePassword)
app.component('service-view-offcanvas', ServiceOffcanvas)

app.mount('[data-render="app"]')
