import { InitApp } from '@/helpers/main'

import FormOffcanvas from './components/FormOffcanvas.vue'
import CityFormOffcanvas from './components/CityFormOffcanvas.vue'
import StateFormOffcanvas from './components/StateFormOffcanvas.vue'

const app = InitApp()

app.component('form-offcanvas', FormOffcanvas)
app.component('city-form-offcanvas', CityFormOffcanvas)
app.component('state-form-offcanvas', StateFormOffcanvas)

app.mount('[data-render="app"]');
