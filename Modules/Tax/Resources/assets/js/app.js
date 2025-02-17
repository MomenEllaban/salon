import { InitApp } from '@/helpers/main'

import TaxFormOffcanvas from './components/TaxFormOffcanvas.vue'

const app = InitApp()

app.component('tax-form-offcanvas', TaxFormOffcanvas)

app.mount('[data-render="app"]');
