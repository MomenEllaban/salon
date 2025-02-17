import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'

const app = InitApp()

app.component('form-offcanvas', FormOffcanvas)

app.mount('[data-render="app"]');
