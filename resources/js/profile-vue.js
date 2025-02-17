import { InitApp } from '@/helpers/main'
import {router} from './Profile/Router'
import LayoutView from './Profile/LayoutView.vue'
import VueTelInput from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const app = InitApp()
app.use(VueTelInput)

const settingPage = InitApp(LayoutView)

settingPage.use(router)

settingPage.mount('#profile-app');
