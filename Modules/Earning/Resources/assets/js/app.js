import { InitApp } from '@/helpers/main'

import EarningFormOffcanvas from './components/EarningFormOffcanvas.vue'

const app = InitApp()

app.component('earning-form-offcanvas', EarningFormOffcanvas)

app.mount('[data-render="app"]');
