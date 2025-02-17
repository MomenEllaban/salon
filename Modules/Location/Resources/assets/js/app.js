import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import FormStocks from './components/FormStocks.vue'

const app = InitApp()

app.component('form-offcanvas', FormOffcanvas)
app.component('form-stocks', FormStocks)

app.mount('[data-render="app"]');
