import { InitApp } from '@/helpers/main'
import {router} from './Setting/Router'
import LayoutView from './Setting/LayoutView.vue'
const settingPage = InitApp(LayoutView)

settingPage.use(router)

settingPage.mount('#setting-app');
